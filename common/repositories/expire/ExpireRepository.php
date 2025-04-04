<?php

namespace common\repositories\expire;

use frontend\models\work\order\ExpireWork;
use frontend\models\work\order\OrderMainWork;
use common\models\scaffold\Expire;
use common\repositories\order\OrderMainRepository;
use common\repositories\regulation\RegulationRepository;
use frontend\models\work\regulation\RegulationWork;
use Yii;

class ExpireRepository
{
    public OrderMainRepository $orderMainRepository;
    public RegulationRepository $regulationRepository;
    public function __construct(
        OrderMainRepository $orderMainRepository,
        RegulationRepository $regulationRepository
    )
    {
        $this->orderMainRepository = $orderMainRepository;
        $this->regulationRepository = $regulationRepository;
    }
    public function prepareCreate($active_regulation_id, $expire_regulation_id, $expire_order_id,
                                    $document_type, $expire_type){
        $model = ExpireWork::fill($active_regulation_id,$expire_regulation_id,
                                    $expire_order_id,$document_type, $expire_type);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(ExpireWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }
    public function getExpireByActiveRegulationId($id){
        return ExpireWork::find()->where(['active_regulation_id'=>$id])->all();
    }
    public function get($id){
        return ExpireWork::find()->where(['id'=>$id])->one();
    }
    public function deleteByActiveRegulationId($id){
        /* @var ExpireWork $model */
        $model = $this->get($id);
        $model->delete();
    }
    public function checkUnique($modelId, $reg, $order, $type, $status){
        $model = ExpireWork::find()
            ->andWhere(['active_regulation_id' => $modelId])
            ->andWhere(['expire_regulation_id' => $reg])
            ->andWhere(['expire_order_id' => $order])
            ->andWhere(['document_type' => $type])
            ->andWhere(['expire_type' => $status])
            ->exists();
        return !$model;
    }
}