<?php

namespace common\repositories\order;

use frontend\models\work\order\OrderEventWork;
use frontend\models\work\order\OrderMainWork;
use DomainException;
use yii\web\UploadedFile;

class OrderEventRepository
{
    public function get($id)
    {
        return OrderEventWork::find()->where(['id' => $id])->one();
    }
    public function delete($id)
    {
        return OrderEventWork::deleteAll(['id' => $id]);
    }
    public function getAll()
    {
        return OrderEventWork::find()->all();
    }

    public function getEventOrdersByLastTime($lastDate)
    {
        return OrderEventWork::find()->where(['type' => OrderMainWork::ORDER_EVENT])->andWhere(['>=', 'order_date', $lastDate])->all();
    }

    public function save(OrderEventWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}