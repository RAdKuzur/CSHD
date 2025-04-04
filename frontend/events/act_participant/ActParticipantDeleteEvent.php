<?php

namespace app\events\act_participant;

use common\events\EventInterface;
use common\repositories\act_participant\ActParticipantRepository;
use Yii;

class ActParticipantDeleteEvent implements EventInterface
{
    public $id;
    private ActParticipantRepository $actParticipantRepository;
    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->actParticipantRepository = Yii::createObject(ActParticipantRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute() {
        return
            [
                $this->actParticipantRepository->prepareDelete($this->id)
            ];
    }
}