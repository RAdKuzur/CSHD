<?php

namespace common\repositories\order;

use frontend\models\work\order\DocumentOrderWork;
use Yii;
use yii\db\ActiveQuery;

class DocumentOrderRepository
{
    public function get($id)
    {
        return DocumentOrderWork::findOne($id);
    }
    public function getAll()
    {
        return DocumentOrderWork::find()->all();
    }
    public function getAllActual($type)
    {
        return DocumentOrderWork::find()->where(['type' => $type])->andWhere(['state' => DocumentOrderWork::ACTUAL])->all();
    }

    public function getAllMain()
    {
        return DocumentOrderWork::find()->where(['type' => DocumentOrderWork::ORDER_MAIN])->all();
    }

    public function getExceptByIdAndStatus($id, $type)
    {
        return DocumentOrderWork::find()->andWhere(['<>', 'id', $id])->andWhere(['type' => $type])->andWhere(['state' => DocumentOrderWork::ACTUAL])->all();
    }

    public function filterByBranch(ActiveQuery $query, int $branch)
    {
        $nomenclatures = array_keys(Yii::$app->nomenclature->getListByBranch($branch));
        return $query->andWhere(['IN', 'order_number', $nomenclatures]);
    }

    public function prepareDelete($id){
        $command = Yii::$app->db->createCommand();
        $command->delete(DocumentOrderWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }
    public function prepareChangeStatus($id){
        $status = (DocumentOrderWork::findOne($id))->state;
        $command = Yii::$app->db->createCommand();
        $state = ($status == DocumentOrderWork::ACTUAL ? DocumentOrderWork::NOT_ACTUAL : DocumentOrderWork::ACTUAL);
        $command->update(DocumentOrderWork::tableName(), ['state' => $state], ['id' => $id]);
        return $command->getRawSql();
    }
}