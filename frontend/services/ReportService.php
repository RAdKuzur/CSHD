<?php

namespace frontend\services;

use common\helpers\creators\ExcelCreator;
use common\services\general\errors\ErrorService;
use frontend\forms\analytics\AnalyticErrorForm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use common\helpers\DateFormatter;
use Yii;

class ReportService
{
    private ErrorService $errorService;

    public function __construct(ErrorService $errorService)
    {
        $this->errorService = $errorService;
    }

    /**
     * Отчёт для конкретного пользователя (ровно то, что он видит на странице аналитики)
     */
    public function prepareErrorsReportByUser(int $userId): ?Spreadsheet
    {
        return $this->buildReport(
            $this->errorService->getErrorsByUser($userId),
            "Отчет_пользователь_{$userId}"
        );
    }

    /**
     * Отчёт по отделу на основе полной выборки администратора
     */
    public function prepareErrorsReportByBranch(int $branch): ?Spreadsheet
    {
        $adminId = $this->getAdminUserId();
        $allVisibleErrors = $this->errorService->getErrorsByUser($adminId);

        $errors = array_filter(
            $allVisibleErrors,
            fn($e) => (int)$e->branch === $branch
        );

        return $this->buildReport($errors, "Отчет_по_филиалу_{$branch}");
    }

    /**
     * Универсальный метод: ошибки, отфильтрованные по заданной callable
     */
    public function prepareFilteredReport(callable $filter, string $reportName): ?Spreadsheet
    {
        $allErrors = $this->errorService->getErrorsByUser($this->getAdminUserId());
        if (empty($allErrors)) {
            return null;
        }

        $filtered = array_filter($allErrors, $filter);
        if (empty($filtered)) {
            return null;
        }

        return $this->createExcelReport($filtered, $reportName);
    }

    /**
     * Отчёт: только мероприятия и учёт достижений (без привязки к отделу)
     */
    public function prepareEventsAndAchievementsReport(): ?Spreadsheet
    {
        $allowedTables = [
            \frontend\models\work\event\EventWork::tableName(),
            \frontend\models\work\event\ForeignEventWork::tableName(),
            \frontend\models\work\team\ActParticipantWork::tableName(),
        ];

        return $this->prepareFilteredReport(
            function ($error) use ($allowedTables) {
                return in_array($error->table_name, $allowedTables);
            },
            'Ошибки_мероприятий_и_достижений'
        );
    }

    /**
     * Отчёт: только приказы основной деятельности (все отделы)
     */
    public function prepareMainActivityOrdersReport(): ?Spreadsheet
    {
        $allErrors = $this->errorService->getErrorsByUser($this->getAdminUserId());
        if (empty($allErrors)) {
            return null;
        }

        $orderRepo = Yii::createObject(\common\repositories\order\DocumentOrderRepository::class);

        $filtered = array_filter($allErrors, function ($error) use ($orderRepo) {
            if ($error->table_name !== \frontend\models\work\order\DocumentOrderWork::tableName()) {
                return false;
            }
            /** @var \frontend\models\work\order\DocumentOrderWork|null $order */
            $order = $orderRepo->get($error->table_row_id);
            return $order !== null && $order->isMain();
        });

        if (empty($filtered)) {
            return null;
        }

        return $this->createExcelReport($filtered, 'Ошибки_приказов_основной_деятельности');
    }

    /**
     * Отчёт по одному или нескольким отделам с фильтрацией по типам сущностей и дополнительному условию.
     *
     * @param int[]    $branches    
     * @param string[] $allowedTables table_name 
     * @param callable|null $extraFilter
     * @param string   $reportName
     */
    public function prepareMultiBranchReportByTables(
        array $branches,
        array $allowedTables,
        ?callable $extraFilter = null,
        string $reportName = 'report'
    ): ?Spreadsheet {
        // Загружаем ВСЕ ошибки ОДИН раз
        $allErrors = $this->errorService->getErrorsByUser($this->getAdminUserId());
        if (empty($allErrors)) {
            return null;
        }

        $collectedErrors = [];
        foreach ($branches as $branch) {
            $branchErrors = $this->collectBranchErrors($allErrors, $branch, $allowedTables, $extraFilter);
            if (!empty($branchErrors)) {
                $collectedErrors = array_merge($collectedErrors, $branchErrors);
            }
        }

        if (empty($collectedErrors)) {
            return null;
        }

        $uniqueErrors = [];
        foreach ($collectedErrors as $error) {
            $uniqueErrors[$error->id] = $error;
        }
        $collectedErrors = array_values($uniqueErrors);

        return $this->createExcelReport($collectedErrors, $reportName);
    }

    /**
     * Возвращает ошибки одного отдела после применения фильтров.
     * Принимает уже загруженный массив ВСЕХ ошибок.
     */
    private function collectBranchErrors(array $allErrors, int $branch, array $allowedTables, ?callable $extraFilter): array
    {
        $orderRepo = null;
        if (in_array(\frontend\models\work\order\DocumentOrderWork::tableName(), $allowedTables) && $extraFilter) {
            $orderRepo = Yii::createObject(\common\repositories\order\DocumentOrderRepository::class);
        }

        return array_filter($allErrors, function ($error) use ($branch, $allowedTables, $extraFilter, $orderRepo) {
            if ((int)$error->branch !== $branch) return false;
            if (!in_array($error->table_name, $allowedTables)) return false;
            if ($extraFilter) {
                return $extraFilter($error, $orderRepo);
            }
            return true;
        });
    }

    /**
     * Возвращает ID пользователя, у которого есть право 'get_all_errors'
     */
    private function getAdminUserId(): int
    {
        try {
            $user = \common\models\User::find()
                ->joinWith('permissions')
                ->where(['permissions.function_id' => 60])
                ->one();
            if ($user && $user->id) {
                return (int)$user->id;
            }
        } catch (\Throwable $e) {
            Yii::error("Не работает метод: " . $e->getMessage(), 'report');
        }

        return 17;
    }


    private function buildReport(array $errors, string $name): ?Spreadsheet
    {
        if (!$errors) {
            return null;
        }
        return $this->createExcelReport($errors, $name);
    }

    private function createExcelReport(array $errors, string $reportName): Spreadsheet
    {
        $errors = array_filter($errors, function ($e) {
            return $e->was_amnesty == 0;
        });

        $analyticForm = new AnalyticErrorForm($errors);

        $categories = [
            'Учебные группы'          => $analyticForm->getGroupErrors(),
            'Образовательные программы' => $analyticForm->getProgramErrors(),
            'Приказы'                 => $analyticForm->getOrderErrors(),
            'Мероприятия'             => $analyticForm->getEventErrors(),
            'Учёт достижений'         => $analyticForm->getForeignEventErrors(),
            'Участники деятельности'   => $analyticForm->getForeignEventParticipantsErrors(),
        ];

        $sheetsData = [];

        $columns = $this->getColumns();
        foreach ($categories as $title => $errorList) {
            $headers = ['Код', 'Описание', 'Дата','Место возникновения', 'Ссылка на возникновение', 'Отдел'];
            $rows = [];
            $columns = $this->getColumns();

            foreach ($errorList as $errorWork) {
                $row = [];
                foreach ($columns as $col) {
                    $row[] = $col($errorWork);
                }
                $rows[] = $row;
            }

            //Не добавляем пустые листы
            if (empty($rows)) {
                continue;
            }

            $sheetsData[$title] = array_merge([$headers], $rows);
        }

        return ExcelCreator::createMultiSheetExcel($sheetsData);
    }

   private function getColumns(): array
    {
        return [
            'code' => function ($e) {
                return Yii::$app->errors->get($e->error)->code ?? 'N/A';
            },
            'description' => function ($e) {
                return Yii::$app->errors->get($e->error)->description ?? '-';
            },
            'date' => function ($e) {
                $date = explode(' ', $e->create_datetime)[0];
                $time = explode(' ', $e->create_datetime)[1];
                return DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot)
                    . ' в ' .
                    DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
            },
            'entity_name' => function ($e) {
                $data = $e->getEntityNameForExport();
                return is_array($data) ? $data['text'] : $data;
            },
            'entity_url' => function ($e) {
                $data = $e->getEntityNameForExport();
                return is_array($data) ? $data['url'] : '';
            },
            'branch' => function ($e) {
                return Yii::$app->branches->get($e->branch) ?? '-';
            },
        ];
    }
}