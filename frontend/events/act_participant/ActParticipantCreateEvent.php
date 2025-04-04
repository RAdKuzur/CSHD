<?php

namespace frontend\events\act_participant;

use common\events\EventInterface;
use common\repositories\act_participant\ActParticipantRepository;
use Yii;

class ActParticipantCreateEvent implements EventInterface
{
    public $data;
    public $teamNameId;
    public $foreignEventId;

    private ActParticipantRepository $actParticipantRepository;
    public function __construct(
        $data,
        $teamNameId,
        $foreignEventId
    )
    {
        $this->data = $data;
        $this->teamNameId = $teamNameId;
        $this->foreignEventId = $foreignEventId;
        $this->actParticipantRepository = Yii::createObject(ActParticipantRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute() {
        return
            $this->actParticipantRepository->prepareCreate(
                $this->data,
                $this->teamNameId,
                $this->foreignEventId
            )
        ;
    }
}