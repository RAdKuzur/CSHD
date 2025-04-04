<?php

namespace frontend\models\work\event;

use common\models\scaffold\EventBranch;

class EventBranchWork extends EventBranch
{
    public static function fill($eventId, $branch)
    {
        $entity = new static();
        $entity->event_id = $eventId;
        $entity->branch = $branch;

        return $entity;
    }
}