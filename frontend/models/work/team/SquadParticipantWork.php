<?php

namespace frontend\models\work\team;

use common\models\scaffold\SquadParticipant;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use Yii;

/**
 * @property ForeignEventParticipantsWork $participantWork
 * @property ActParticipantWork $actParticipantWork
 */
class SquadParticipantWork extends SquadParticipant
{
    public static function fill(
        $actParticipant,
        $participantId
    ){
        $entity = new static();
        $entity->act_participant_id = $actParticipant;
        $entity->participant_id = $participantId;
        return $entity;
    }

    public function getActString()
    {
        return $this->participantWork->getFullFio() . ' (' . $this->actParticipantWork->getString(). ')';
    }

    public function getActParticipantWork()
    {
        return $this->hasOne(ActParticipantWork::class, ['id' => 'act_participant_id']);
    }

    public function getParticipantWork()
    {
        return $this->hasOne(ForeignEventParticipantsWork::class, ['id' => 'participant_id']);
    }

    public function setParticipantId(int $participantId)
    {
        $this->participant_id = $participantId;
    }
}