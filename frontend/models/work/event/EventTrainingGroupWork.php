<?php

namespace frontend\models\work\event;

use common\models\scaffold\EventTrainingGroup;

class EventTrainingGroupWork extends EventTrainingGroup
{
    public static function fill($eventId, $groupID)
    {
        $entity = new static();
        $entity->event_id = $eventId;
        $entity->training_group_id = $groupID;

        return $entity;
    }
}