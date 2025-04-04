<?php

namespace app\events\act_participant;

use common\events\EventInterface;
use common\repositories\act_participant\SquadParticipantRepository;

class SquadParticipantDeleteByIdEvent implements EventInterface
{
    public $id;
    private SquadParticipantRepository $squadParticipantRepository;
    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->squadParticipantRepository = new SquadParticipantRepository();
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute() {
        return
            [
                $this->squadParticipantRepository->prepareDeleteByActId($this->id)
            ];
    }
}