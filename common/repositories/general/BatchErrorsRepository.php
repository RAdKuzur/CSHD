<?php

namespace common\repositories\general;

use common\models\Error;
use common\models\work\ErrorsWork;
use Yii;

/**
 * Batch-обертка для ErrorsRepository.
 * Накапливает операции save/delete/update и выполняет их массово.
 * Не наследует ErrorsRepository, а делегирует ему.
 */
class BatchErrorsRepository implements ErrorsRepositoryInterface
{
    /** @var ErrorsWork[] - ошибки для создания */
    private array $pendingSaves = [];

    /** @var array - ID ошибок для удаления */
    private array $pendingDeletes = [];

    /** @var array - ошибки для обновления состояния [id => newState] */
    private array $pendingUpdates = [];

    private bool $batchMode = false;

    private ErrorsRepository $baseRepository;

    /** @var array - кэш для проверки дубликатов при batch-вставке */
    private array $duplicateCheckCache = [];

    public function __construct(ErrorsRepository $baseRepository)
    {
        $this->baseRepository = $baseRepository;
    }

    /**
     * Включаем режим накопления
     */
    public function enableBatchMode(): void
    {
        $this->batchMode = true;
        $this->pendingSaves = [];
        $this->pendingDeletes = [];
        $this->pendingUpdates = [];
        $this->duplicateCheckCache = [];
    }

    /**
     * Выключаем режим накопления и сбрасываем накопленное
     */
    public function disableBatchMode(): void
    {
        $this->batchMode = false;
        $this->clear();
    }

    /**
     * Проверяем, включен ли batch-режим
     */
    public function isBatchMode(): bool
    {
        return $this->batchMode;
    }

    /**
     * Очищаем накопленные данные
     */
    public function clear(): void
    {
        $this->pendingSaves = [];
        $this->pendingDeletes = [];
        $this->pendingUpdates = [];
        $this->duplicateCheckCache = [];
    }

    /**
     * Накопление или выполнение save
     */
    public function save(ErrorsWork $model)
    {
        if (!$this->batchMode) {
            return $this->baseRepository->save($model);
        }

        // Формируем ключ для проверки дубликатов
        $key = $model->table_name . '_' . $model->table_row_id . '_' . $model->error;

        // Проверяем, нет ли уже такой ошибки в очереди на сохранение
        if (isset($this->duplicateCheckCache[$key])) {
            return false;
        }

        // Проверяем, не удаляется ли эта ошибка
        if ($model->id && in_array($model->id, $this->pendingDeletes)) {
            return false;
        }

        $this->pendingSaves[] = $model;
        $this->duplicateCheckCache[$key] = true;

        return true;
    }

    /**
     * Накопление или выполнение delete
     */
    public function delete(ErrorsWork $model)
    {
        if (!$this->batchMode) {
            return $this->baseRepository->delete($model);
        }

        $key = $model->table_name . '_' . $model->table_row_id . '_' . $model->error;

        // Удаляем из очереди на сохранение, если была
        if (isset($this->duplicateCheckCache[$key])) {
            unset($this->duplicateCheckCache[$key]);
            $this->pendingSaves = array_filter(
                $this->pendingSaves,
                function (ErrorsWork $pending) use ($key) {
                    $pendingKey = $pending->table_name . '_' . $pending->table_row_id . '_' . $pending->error;
                    return $pendingKey !== $key;
                }
            );
        }

        if ($model->id) {
            $this->pendingDeletes[] = $model->id;
        }

        return true;
    }

    /**
     * Накопление или выполнение обновления состояния
     */
    public function updateState(ErrorsWork $model, int $newState): void
    {
        if (!$this->batchMode) {
            $model->state = $newState;
            $this->baseRepository->save($model);
            return;
        }

        $this->pendingUpdates[$model->id] = $newState;
    }

    /**
     * Применяем все накопленные операции одной транзакцией
     */
    public function flush(): array
    {
        if (!$this->batchMode) {
            return ['saved' => 0, 'deleted' => 0, 'updated' => 0];
        }

        $result = [
            'saved' => 0,
            'deleted' => 0,
            'updated' => 0,
        ];

        if (empty($this->pendingSaves)
            && empty($this->pendingDeletes)
            && empty($this->pendingUpdates)) {
            return $result;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 1. Сначала удаляем
            if (!empty($this->pendingDeletes)) {
                // Убираем дубликаты
                $uniqueDeletes = array_unique($this->pendingDeletes);
                $result['deleted'] = $this->baseRepository->deleteByListId($uniqueDeletes);
            }

            // 2. Затем создаем новые (batch insert)
            if (!empty($this->pendingSaves)) {
                $result['saved'] = $this->batchInsertErrors($this->pendingSaves);
            }

            // 3. Обновляем состояния
            if (!empty($this->pendingUpdates)) {
                $result['updated'] = $this->batchUpdateStates($this->pendingUpdates);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('BatchErrorsRepository flush error: ' . $e->getMessage());
            throw $e;
        }

        // Очищаем после применения
        $this->clear();

        return $result;
    }

    /**
     * Массовая вставка ошибок через batchInsert
     */
    private function batchInsertErrors(array $errors): int
    {
        if (empty($errors)) {
            return 0;
        }

        // Проверяем существующие ошибки в БД одним запросом
        $existingKeys = $this->getExistingErrorKeys($errors);

        $rows = [];
        $now = date('Y-m-d H:i:s');

        foreach ($errors as $error) {
            $key = $error->table_name . '_' . $error->table_row_id . '_' . $error->error;

            // Пропускаем если уже существует в БД
            if (isset($existingKeys[$key])) {
                continue;
            }

            $rows[] = [
                $error->error,           // error
                $error->table_name,      // table_name
                $error->table_row_id,    // table_row_id
                $error->state ?? 0,      // state (должно быть число, не дата!)
                $error->branch ?? null,  // branch
                0,                       // was_amnesty
                $now,                    // create_datetime
            ];
        }

        if (!empty($rows)) {
            Yii::$app->db->createCommand()->batchInsert(
                ErrorsWork::tableName(),
                ['error', 'table_name', 'table_row_id', 'state', 'branch', 'was_amnesty', 'create_datetime'],
                $rows
            )->execute();

            return count($rows);
        }

        return 0;
    }

    /**
     * Массовое обновление состояний ошибок
     */
    private function batchUpdateStates(array $updates): int
    {
        if (empty($updates)) {
            return 0;
        }

        $updated = 0;

        // Группируем по новому состоянию
        $grouped = [];
        foreach ($updates as $id => $state) {
            $grouped[$state][] = $id;
        }

        foreach ($grouped as $state => $ids) {
            $count = ErrorsWork::updateAll(
                ['state' => $state],
                ['IN', 'id', array_unique($ids)]
            );
            $updated += $count;
        }

        return $updated;
    }

    /**
     * Получаем существующие ключи ошибок одним запросом
     */
    private function getExistingErrorKeys(array $errors): array
    {
        if (empty($errors)) {
            return [];
        }

        $conditions = [];
        foreach ($errors as $error) {
            $conditions[] = [
                'table_name' => $error->table_name,
                'table_row_id' => $error->table_row_id,
                'error' => $error->error,
                'was_amnesty' => 0,
            ];
        }

        // Если слишком много условий, разбиваем на части
        if (count($conditions) > 1000) {
            return $this->getExistingErrorKeysChunked($conditions);
        }

        return $this->queryExistingKeys($conditions);
    }

    /**
     * Запрос существующих ключей (до 1000 условий)
     */
    private function queryExistingKeys(array $conditions): array
    {
        if (empty($conditions)) {
            return [];
        }

        $query = ErrorsWork::find()
            ->select(['CONCAT(table_name, "_", table_row_id, "_", error) as composite_key'])
            ->where(['was_amnesty' => 0]);

        // Строим OR условия
        $orConditions = ['OR'];
        foreach ($conditions as $condition) {
            $orConditions[] = [
                'table_name' => $condition['table_name'],
                'table_row_id' => $condition['table_row_id'],
                'error' => $condition['error'],
            ];
        }
        $query->andWhere($orConditions);

        $existing = $query->asArray()->column();

        return array_flip($existing);
    }

    /**
     * Разбиваем на чанки для большого количества условий
     */
    private function getExistingErrorKeysChunked(array $conditions): array
    {
        $result = [];
        $chunks = array_chunk($conditions, 500);

        foreach ($chunks as $chunk) {
            $result += $this->queryExistingKeys($chunk);
        }

        return $result;
    }

    /**
     * Делегируем остальные методы базовому репозиторию
     */
    public function __call($method, $args)
    {
        if (method_exists($this->baseRepository, $method)) {
            return call_user_func_array([$this->baseRepository, $method], $args);
        }

        throw new \BadMethodCallException("Method {$method} does not exist");
    }

    /**
     * Явно делегированные методы (для типизации и автодополнения в IDE)
     */
    public function get(int $id)
    {
        return $this->baseRepository->get($id);
    }

    public function getChangeableErrors()
    {
        return $this->baseRepository->getChangeableErrors();
    }

    public function getErrorsByTableName(string $tableName)
    {
        return $this->baseRepository->getErrorsByTableName($tableName);
    }

    public function getErrorsByTableRow(string $tableName, int $rowId)
    {
        return $this->baseRepository->getErrorsByTableRow($tableName, $rowId);
    }

    public function getAmnestyErrorsByTableRow(string $tableName, int $rowId)
    {
        return $this->baseRepository->getAmnestyErrorsByTableRow($tableName, $rowId);
    }

    public function getErrorsByTableRowsBranchTypes(string $tableName, array $rowIds, int $branch = null, array $types = [Error::TYPE_BASE, Error::TYPE_CRITICAL])
    {
        return $this->baseRepository->getErrorsByTableRowsBranchTypes($tableName, $rowIds, $branch, $types);
    }

    public function getErrorsByTableRowError(string $tableName, int $rowId, string $error)
    {
        return $this->baseRepository->getErrorsByTableRowError($tableName, $rowId, $error);
    }

    public function getErrorByTableName($tableName)
    {
        return $this->baseRepository->getErrorByTableName($tableName);
    }

    public function getErrorsIdsByTableName($tableName)
    {
        return $this->baseRepository->getErrorsIdsByTableName($tableName);
    }

    public function getQueryForErrorsByTable(string $tableName)
    {
        return $this->baseRepository->getQueryForErrorsByTable($tableName);
    }

    public function deleteByListId(array $ids)
    {
        return $this->baseRepository->deleteByListId($ids);
    }

    public function saveMultiple(array $models)
    {
        return $this->baseRepository->saveMultiple($models);
    }

    /**
     * Получаем информацию о накопленных операциях
     */
    public function getPendingCount(): array
    {
        return [
            'to_save' => count($this->pendingSaves),
            'to_delete' => count($this->pendingDeletes),
            'to_update' => count($this->pendingUpdates),
        ];
    }
}