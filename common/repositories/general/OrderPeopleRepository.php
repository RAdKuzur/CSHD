<?php

namespace common\repositories\general;

use frontend\models\work\general\OrderPeopleWork;
use Yii;

class OrderPeopleRepository
{
    public function get($id)
    {
        return OrderPeopleWork::find()->where(['id' => $id])->one();
    }

    public function deleteByPeopleId($id)
    {
        $model = OrderPeopleWork::getByPeopleId($id);
        foreach ($model as $one) {
            /** @var OrderPeopleWork $one */
            $one->delete();
        }
    }

    public function getResponsiblePeople($id)
    {
        return OrderPeopleWork::find()->where(['order_id' => $id])->all();
    }

    public function checkUnique($people_id, $order_id){
        $model = OrderPeopleWork::find()->andWhere(['people_id' => $people_id, 'order_id' => $order_id])->one();
        return $model ? false : true;
    }

    public function prepareCreate($people_id, $order_id){
        $model = OrderPeopleWork::fill($people_id, $order_id);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($people_id, $order_id){
        $model = OrderPeopleWork::find()->andWhere(['people_id' => $people_id, 'order_id' => $order_id])->one();
        $command = Yii::$app->db->createCommand();
        $command->delete($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
    public function prepareDeleteById($id){
        $command = Yii::$app->db->createCommand();
        $command->delete(OrderPeopleWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }
}