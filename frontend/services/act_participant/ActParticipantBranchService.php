<?php

namespace frontend\services\act_participant;

use frontend\events\act_participant\SquadParticipantCreateEvent;
use frontend\models\work\team\ActParticipantBranchWork;
use frontend\models\work\team\ActParticipantWork;
use common\models\scaffold\ActParticipantBranch;
use common\repositories\act_participant\ActParticipantBranchRepository;

class ActParticipantBranchService
{
    private ActParticipantBranchRepository $actParticipantBranchRepository;
    public function __construct(
        ActParticipantBranchRepository $actParticipantBranchRepository
    )
    {
        $this->actParticipantBranchRepository = $actParticipantBranchRepository;
    }
    public function addActParticipantBranchEvent($actId, $branch ){
        $this->actParticipantBranchRepository->prepareCreate($actId, $branch);
    }
}