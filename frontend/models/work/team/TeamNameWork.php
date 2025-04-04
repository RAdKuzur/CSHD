<?php

namespace frontend\models\work\team;

use common\events\EventTrait;
use common\models\scaffold\TeamName;

class TeamNameWork extends TeamName
{
    use EventTrait;
    public static function fill($name, $foreignEventId)
    {
        $entity = new static();
        $entity->name = $name;
        $entity->foreign_event_id = $foreignEventId;
        return $entity;
    }
}