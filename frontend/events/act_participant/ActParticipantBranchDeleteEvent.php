<?php

namespace app\events\act_participant;

use common\events\EventInterface;
use common\repositories\act_participant\ActParticipantBranchRepository;

class ActParticipantBranchDeleteEvent implements EventInterface
{
    public $id;
    private ActParticipantBranchRepository $actParticipantBranchRepository;
    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->actParticipantBranchRepository = new ActParticipantBranchRepository();
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute() {
        return
            [
                $this->actParticipantBranchRepository->prepareDeleteByAct($this->id)
            ];
    }
}