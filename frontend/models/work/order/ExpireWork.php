<?php

namespace frontend\models\work\order;

use common\events\EventTrait;
use common\models\scaffold\Expire;
use frontend\models\work\regulation\RegulationWork;

/* @property RegulationWork $expireRegulationWork */
/* @property DocumentOrderWork $expireOrderWork */

class ExpireWork extends Expire
{
    use EventTrait;

    const DENY_TYPE = 1;
    const CHANGE_TYPE = 2;

    public static function fill(
        $active_regulation_id,
        $expire_regulation_id,
        $expire_order_id,
        $document_type,
        $expire_type
    )
    {
        $entity = new static();
        $entity->active_regulation_id = $active_regulation_id;
        $entity->expire_regulation_id = $expire_regulation_id;
        $entity->expire_order_id = $expire_order_id;
        $entity->document_type = $document_type;
        $entity->expire_type = $expire_type;
        return $entity;
    }
    public function getStatus() {
        return $this->expire_type == 1 ? 'Утратило силу' : 'Изменено';
    }
    public function getNumber()
    {
        $info = NULL;
        if($this->expire_order_id){
            /* @var OrderMainWork $order */
            $order = OrderMainWork::find()->where(['id' => $this->expire_order_id])->one();
            $info = $order->order_name;
        }
        if($this->expire_regulation_id){
            /* @var RegulationWork $regulation */
            $regulation = RegulationWork::find()->where(['id' => $this->expire_regulation_id])->one();
            $info = $regulation->name;
        }
        return $info;
    }
    public function getType(
    ) {
        return $this->expire_regulation_id == NULL ? 'Приказ' : 'Положение';
    }
    public function customLoad($post , $modelId)
    {
        $this->active_regulation_id = $modelId;
        $this->expire_regulation_id = $post['expireRegulationId'];
        $this->expire_order_id = $post['expireOrderId'];
        $this->document_type = DocumentOrderWork::ORDER_MAIN;
        $this->expire_type = $post['expireType'];
    }

    public function getExpireRegulationWork()
    {
        return $this->hasOne(RegulationWork::class, ['id' => 'expire_regulation_id']);
    }

    public function getExpireOrderWork()
    {
        return $this->hasOne(DocumentOrderWork::class, ['id' => 'expire_order_id']);
    }
}