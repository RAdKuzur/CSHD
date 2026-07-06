<?php

namespace common\repositories\general;

use common\models\Error;
use common\models\work\ErrorsWork;
use DomainException;
use Yii;

class ErrorsRepository implements ErrorsRepositoryInterface
{
    public function get(int $id)
    {
        return ErrorsWork::find()->where(['id' => $id])->one();
    }

    public function getChangeableErrors()
    {
        $errors = ErrorsWork::find()->all();
        return array_filter($errors, function (ErrorsWork $error) {
            /** @var Error $entity */
            $entity = Yii::$app->errors->get($error->error);
            return $entity->isChangeable();
        });
    }

    public  function getErrorsByTableName(string $tableName)
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['was_amnesty' => 0])
            ->all();
    }

    public function getErrorsByTableRow(string $tableName, int $rowId)
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['table_row_id' => $rowId])
            ->andWhere(['was_amnesty' => 0])
            ->all();
    }

    /**
     * Получить все амнистированные ошибки таблицы
     */
    public function getAllAmnestyErrorsByTableName(string $tableName): array
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['was_amnesty' => 1])
            ->all();
    }

    public function getAmnestyErrorsByTableRow(string $tableName, int $rowId)
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['table_row_id' => $rowId])
            ->andWhere(['was_amnesty' => 1])
            ->all();
    }

    public function getErrorsByTableRowsBranchTypes(string $tableName, array $rowIds, int $branch = null, array $types = [Error::TYPE_BASE, Error::TYPE_CRITICAL])
    {
        $query = ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['IN', 'table_row_id', $rowIds])
            ->andWhere(['was_amnesty' => 0])
            ->andWhere(['IN', 'state', $types]);
        if ($branch) {
            $query = $query->andWhere(['branch' => $branch]);
        }

        $query = $query->orderBy(['create_datetime' => SORT_DESC]);
        return $query->all();
    }

    public function getErrorsByTableRowError(string $tableName, int $rowId, string $error)
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['table_row_id' => $rowId])
            ->andWhere(['error' => $error])
            ->andWhere(['was_amnesty' => 0])
            ->one();
    }
    public function getErrorByTableName($tableName)
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['was_amnesty' => 0])  
            ->orderBy(['create_datetime' => SORT_DESC])
            ->all();
    }

    public function getErrorsIdsByTableName($tableName)
    {
        return ErrorsWork::find()
            ->select(['table_row_id', 'id']) // Выбираем оба поля
            ->where(['table_name' => $tableName])
            ->andWhere(['was_amnesty' => 0])
            ->indexBy('id') // Делаем 'id' ключом массива
            ->asArray() // Работаем с массивом для скорости и экономии памяти
            ->column(); // Извлекает первую колонку из select (table_row_id), сохраняя ключи из indexBy
    }

    /**
     * Возвращает объект запроса для поиска ошибок по имени таблицы.
     * Используется для построения сложных или пакетных запросов (batch/column).
     * * @param string $tableName
     * @return \yii\db\ActiveQuery
     */
    public function getQueryForErrorsByTable(string $tableName)
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['was_amnesty' => 0]);
    }

    public function delete(ErrorsWork $model)
    {
        if (!$model->delete()) {
            var_dump($model->getErrors());
        }
        return $model->delete();
    }

    public function deleteByListId(array $ids)
    {
        if (empty($ids)) {
            return 0;
        }

        return ErrorsWork::deleteAll(['id' => $ids]);
    }

    public function save(ErrorsWork $model)
    {
        if (!$this->getErrorsByTableRowError($model->table_name, $model->table_row_id, $model->error) || !is_null($model->id)) {
            if (!$model->save()) {
                throw new DomainException('Ошибка сохранения ошибки данных. Проблемы: '.json_encode($model->getErrors()));
            }
            return $model->id;
        }
        return false;
    }

    public function saveMultiple(array $models)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $insertedIds = [];

            foreach ($models as $model) {
                if (!$model instanceof ErrorsWork) {
                    continue;
                }

                // Ваша проверка на дубликаты
                $exists = $this->getErrorsByTableRowError($model->table_name, $model->table_row_id, $model->error);

                if (!$exists || !is_null($model->id)) {
                    if (!$model->save()) {
                        throw new DomainException('Ошибка группового сохранения. Проблемы: ' . json_encode($model->getErrors()));
                    }
                    $insertedIds[] = $model->id;
                }
            }

            $transaction->commit();
            return $insertedIds; // Возвращаем массив ID сохраненных ошибок
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}