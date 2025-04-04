<?php

namespace frontend\models\work\event;

use common\models\scaffold\EventScope;

class EventScopeWork extends EventScope
{
    public static function fill($eventId, $scope)
    {
        $entity = new static();
        $entity->event_id = $eventId;
        $entity->participation_scope = $scope;

        return $entity;
    }
}