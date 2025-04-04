<?php

namespace common\repositories\log;

use common\models\work\LogWork;
use DomainException;
use yii\db\ActiveQuery;

class LogRepository
{
    public function query()
    {
        return LogWork::find();
    }

    public function findByQuery(ActiveQuery $query)
    {
        return $query->all();
    }

    public function get($id)
    {
        return LogWork::find()->where(['id' => $id])->one();
    }

    /**
     * @param LogWork $model
     * @return int
     * @throws \yii\db\Exception
     */
    public function save(LogWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}