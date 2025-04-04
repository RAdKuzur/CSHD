<?php

namespace common\components\traits;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

trait CommonDatabaseFunctions
{
    /**
     * Проверка возможности удаления записи из таблицы $dependentTable
     * @param string $baseTableName имя таблицы, которая связана с удаляемой
     * @param string $dependentTableName имя таблицы, из которой удаляется запись
     * @param int $entityId ID удаляемой записи
     * @return array
     */
    public function checkDeleteAvailable(string $baseTableName, string $dependentTableName, int $entityId)
    {
        $schema = Yii::$app->db->schema;
        $foreignKeys = $schema->getTableSchema($baseTableName)->foreignKeys;

        $errorStrings = [];

        foreach ($foreignKeys as $fkName => $fkInfo) {
            $keys = array_keys($fkInfo);
            $values = array_values($fkInfo);
            if ($values[0] === $dependentTableName) {
                $relatedDocumentsCount = Yii::$app->db->createCommand()
                    ->setSql("SELECT COUNT(*) FROM {$baseTableName} WHERE {$keys[1]} = :entityId")
                    ->bindValue(':entityId', $entityId)
                    ->queryScalar();

                if ($relatedDocumentsCount > 0) {
                    $name = Yii::$app->tables->get($baseTableName);
                    $key = $keys[1];
                    $errorStrings[] = "Нельзя удалить запись, так как существуют связанные записи в разделе \"{$name}\" ({$key})";
                }
            }
        }

        return $errorStrings;
    }

    public function findDuplicates(ActiveRecord $model, array $fields)
    {
        if (!$model instanceof ActiveRecord || empty($fields)) {
            return false;
        }

        $tableName = $model::tableName();
        $primaryKey = $model->getPrimaryKey(true);

        $query = (new Query())
            ->select(['*'])
            ->from($tableName);

        // Формируем условие исключения текущей записи по первичному ключу
        foreach ($primaryKey as $key => $keyField) {
            if ($model->$key !== null) {
                $query->andWhere(['!=', $key, $model->$key]);
            }
        }

        // Добавляем условия для проверки полей на дублирование
        foreach ($fields as $field) {
            $query->andWhere([$field => $model->$field]);
        }

        $duplicates = $query->all(Yii::$app->db);
        return !empty($duplicates) ? $duplicates : false;
    }
}