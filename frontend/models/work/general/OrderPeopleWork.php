<?php

namespace frontend\models\work\general;

use common\models\scaffold\OrderPeople;

use frontend\models\work\general\PeopleWork;

/* @property PeopleStampWork $peopleStampWork */

class OrderPeopleWork extends OrderPeople
{
    public static function fill(
        $people_id,
        $order_id
    )
    {
        $entity = new static();
        $entity->people_id = $people_id;
        $entity->order_id = $order_id;
        return $entity;
    }
    public function getFullFio(){
        $people = PeopleWork::findOne($this->people_id);
        return $people->firstname.' '.$people->surname.' '.$people->patronymic;
    }
    public static function getByPeopleId($id){
        return OrderPeopleWork::find()->where(['id' => $id])->all();
    }
    public static function getByOrderId($id){
        return OrderPeopleWork::find()->where(['order_id' => $id])->all();
    }

    public function getPeopleStampWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'people_id']);
    }
}