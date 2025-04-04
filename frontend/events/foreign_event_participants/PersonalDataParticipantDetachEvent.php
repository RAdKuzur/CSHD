<?php

namespace frontend\events\foreign_event_participants;

use common\events\EventInterface;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use Yii;

class PersonalDataParticipantDetachEvent implements EventInterface
{
    private $participantId;

    private PersonalDataParticipantRepository $repository;

    public function __construct(
        $participantId
    )
    {
        $this->participantId = $participantId;
        $this->repository = Yii::createObject(PersonalDataParticipantRepository::class);
    }

    public function isSingleton(): bool
    {
        return true;
    }

    public function execute()
    {
        return
            $this->repository->prepareDetachPersonalData(
                $this->participantId
            );
    }
}