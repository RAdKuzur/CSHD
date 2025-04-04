<?php

namespace frontend\events\educational\training_program;

use common\events\EventInterface;
use common\repositories\educational\TrainingProgramRepository;
use Yii;

class DeleteTrainingProgramBranchEvent implements EventInterface
{
    private $programId;

    private TrainingProgramRepository $repository;

    public function __construct(
        $programId
    )
    {
        $this->programId = $programId;
        $this->repository = Yii::createObject(TrainingProgramRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return $this->repository->prepareResetBranches($this->programId);
    }
}