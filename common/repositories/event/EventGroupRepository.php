<?php

namespace common\repositories\event;

use frontend\models\work\event\EventGroupWork;
use Yii;

class EventGroupRepository
{
    public function getGroupsFromEvent(int $eventId)
    {
        return EventGroupWork::find()->where(['event_id' => $eventId])->all();
    }

    public function prepareCreate(int $groupId, int $eventId){
        $model = EventGroupWork::fill($groupId, $eventId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
}