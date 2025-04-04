<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\educational\ProjectThemeRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use Yii;

class UpdateTrainingGroupParticipantEvent implements EventInterface
{
    private $id;
    private $participantId;
    private $sendMethod;

    private TrainingGroupParticipantRepository $repository;

    public function __construct(
        $id,
        $participantId,
        $sendMethod
    )
    {
        $this->id = $id;
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
            $this->repository->prepareUpdate(
                $this->id,
                $this->participantId,
                $this->sendMethod
            )
        ];
    }
}