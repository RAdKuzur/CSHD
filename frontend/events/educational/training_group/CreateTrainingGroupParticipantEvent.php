<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use Yii;

class CreateTrainingGroupParticipantEvent implements EventInterface
{
    private $groupId;
    private $participantId;
    private $sendMethod;

    private TrainingGroupParticipantRepository $repository;

    public function __construct(
        $groupId,
        $participantId,
        $sendMethod
    )
    {
        $this->groupId = $groupId;
        $this->participantId = $participantId;
        $this->sendMethod = $sendMethod;
        $this->repository = Yii::createObject(TrainingGroupParticipantRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareCreate($this->groupId, $this->participantId, $this->sendMethod)
        ];
    }
}