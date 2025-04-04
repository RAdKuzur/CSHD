<?php

namespace app\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\educational\TrainingGroupParticipantRepository;
use Yii;

class UpdateStatusTrainingGroupParticipantEvent implements EventInterface
{
    private $id;

    private $status;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;

    public function __construct(
        $id,
        $status
    )
    {
        $this->id = $id;
        $this->status = $status;
        $this->trainingGroupParticipantRepository = Yii::createObject(TrainingGroupParticipantRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->trainingGroupParticipantRepository->prepareUpdateByStatus($this->id, $this->status)
        ];
    }
}