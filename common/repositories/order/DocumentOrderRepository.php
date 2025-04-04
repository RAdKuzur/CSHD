<?php

namespace common\repositories\order;

use frontend\models\work\order\DocumentOrderWork;
use Yii;

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
    public function getAllByType($type)
    {
        return DocumentOrderWork::find()->where(['type' => $type])->all();
    }
    public function getExceptByIdAndStatus($id, $type){
        return DocumentOrderWork::find()->andWhere(['<>', 'id', $id])->andWhere(['type' => $type])->all();
    }
    public function prepareDelete($id){
        $command = Yii::$app->db->createCommand();
        $command->delete(DocumentOrderWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }
}