<?php

namespace common\repositories\order;

use app\models\work\order\OrderEventGenerateWork;
use common\models\scaffold\OrderEventGenerate;
use DomainException;
use Yii;

class OrderEventGenerateRepository
{
    public function prepareDeleteByOrder($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(OrderEventGenerateWork::tableName(), ['order_id' => $id]);
        return $command->getRawSql();
    }
    public function getByOrderId($orderId)
    {
        return OrderEventGenerateWork::findOne(['order_id' => $orderId]);
    }
    public function save(OrderEventGenerateWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
    public function existsByOrderId($orderId){
        return OrderEventGenerate::find()->where(['order_id' => $orderId])->exists();
    }
}