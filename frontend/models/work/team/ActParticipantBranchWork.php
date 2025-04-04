<?php

namespace frontend\models\work\team;

use common\events\EventTrait;
use common\models\scaffold\ActParticipantBranch;

class ActParticipantBranchWork extends ActParticipantBranch
{
    use EventTrait;
    public static function fill(
        $actParticipantId,
        $branch
    ){
        $entity = new static();
        $entity->act_participant_id = $actParticipantId;
        $entity->branch = $branch;
        return $entity;
    }
}