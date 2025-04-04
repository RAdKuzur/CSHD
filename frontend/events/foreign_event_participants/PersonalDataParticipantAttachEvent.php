<?php

namespace frontend\events\foreign_event_participants;

use common\events\EventInterface;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use Yii;

class PersonalDataParticipantAttachEvent implements EventInterface
{
    private $participantId;
    private $pd;

    private PersonalDataParticipantRepository $repository;

    public function __construct(
        $participantId,
        $pd = []
    )
    {
        $this->participantId = $participantId;
        $this->pd = $pd;
        $this->repository = Yii::createObject(PersonalDataParticipantRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return
            array_merge(
                $this->repository->prepareResetAllPersonalData(
                    $this->participantId
                ),
                $this->repository->prepareAttachPersonalData(
                    $this->participantId,
                    $this->pd
                )
            );
    }
}