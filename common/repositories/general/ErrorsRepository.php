<?php

namespace common\repositories\general;

use common\models\Error;
use common\models\work\ErrorsWork;
use DomainException;
use Yii;

class ErrorsRepository
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

    public function getErrorsByTableRow(string $tableName, int $rowId)
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['table_row_id' => $rowId])
            ->andWhere(['was_amnesty' => 0])
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

    public function delete(ErrorsWork $model)
    {
        if (!$model->delete()) {
            var_dump($model->getErrors());
        }
        return $model->delete();
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
}