<?php

namespace backend\services\report;

use backend\forms\report\DodForm;
use backend\forms\report\ForeignEventReportForm;
use backend\forms\report\ManHoursReportForm;
use backend\forms\report\SAForm;
use backend\services\report\form\DodReportService;
use backend\services\report\form\StateAssignmentReportService;
use backend\services\report\interfaces\ForeignEventServiceInterface;
use backend\services\report\interfaces\ManHoursServiceInterface;
use Yii;
use yii\base\InvalidConfigException;

class ReportFacade
{
    // Режим формирования отчета
    const MODE_PURE = 1; // формирование только отчетных данных. работает быстро
    const MODE_DEBUG = 2; // формирование отчетных данных вместе с подробным исходными данными. работает сильно медленнее MODE_PURE

    /**
     * @param ManHoursReportForm $form
     * @return array
     * @throws InvalidConfigException
     */
    public static function generateManHoursReport(ManHoursReportForm $form, ManHoursServiceInterface $service)
    {
        $manHoursResult = [];
        if ($form->isManHours()) {
            $manHoursResult['manHours'] =
                $service->calculateManHours(
                    $form->startDate,
                    $form->endDate,
                    $form->branch,
                    $form->focus,
                    $form->allowRemote,
                    $form->budget,
                    $form->method,
                    $form->teacher !== '' ? [$form->teacher] : [],
                    $form->mode
                );
        }

        if ($form->isParticipants()) {
            array_shift($form->type);
            $manHoursResult['participants'] =
                $service->calculateParticipantsByPeriod(
                    $form->startDate,
                    $form->endDate,
                    $form->branch,
                    $form->focus,
                    $form->allowRemote,
                    $form->budget,
                    $form->type,
                    $form->unic,
                    $form->teacher !== '' ? [$form->teacher] : [],
                    $form->mode
                );
        }

        return $manHoursResult;
    }

    public static function generateParticipantsReport(ForeignEventReportForm $form, ForeignEventServiceInterface $service)
    {
        return $service->calculateEventParticipants(
            $form->startDate,
            $form->endDate,
            $form->branches,
            $form->focuses,
            $form->allowRemotes,
            $form->levels,
            $form->mode
        );
    }

    /**
     * Основная функция генерации отчета типа ДОД
     *
     * @param DodForm $form
     * @param DodReportService $service
     * @return array
     */
    public static function generateDod(DodForm $form, DodReportService $service) : array
    {
        $result = [];
        $result['section3'] = $service->fillSection3($form->startDate, $form->endDate);
        $result['section4'] = $service->fillSection4($form->startDate, $form->endDate);
        $result['section5'] = $service->fillSection5($form->startDate, $form->endDate);
        $result['section10'] = $service->fillSection10();
        $result['section11'] = $service->fillSection11();

        return $result;
    }

    /**
     * Основная функция генерации отчета типа Гос. Задание
     *
     * @param SAForm $form
     * @param StateAssignmentReportService $service
     * @return array
     */
    public static function generateSA(SAForm $form, StateAssignmentReportService $service) : array
    {
        $result = [];
        $result['section31'] = $service->fillSection31($form->startDate, $form->endDate);
        $result['section32'] = $service->fillSection32($form->startDate, $form->endDate, $form->type);

        return $result;
    }
}