<?php

namespace frontend\models\work\event;

use common\models\scaffold\EventGroup;
use frontend\models\work\educational\training_group\TrainingGroupWork;

/**
 * @property TrainingGroupWork $trainingGroupWork
 */
class EventGroupWork extends EventGroup
{

    public function rules()
    {
        return [
            ['training_group_id', 'integer']
        ];
    }

    public static function fill(int $groupId, int $eventId)
    {
        $entity = new static();
        $entity->training_group_id = $groupId;
        $entity->event_id = $eventId;

        return $entity;
    }

    public function getTrainingGroupWork()
    {
        return $this->hasOne(TrainingGroupWork::class, ['id' => 'training_group_id']);
    }
}