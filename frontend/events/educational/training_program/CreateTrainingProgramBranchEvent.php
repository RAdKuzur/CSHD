<?php

namespace frontend\events\educational\training_program;

use common\events\EventInterface;
use common\repositories\educational\TrainingProgramRepository;
use Yii;

class CreateTrainingProgramBranchEvent implements EventInterface
{
    private $programId;
    private $branches;

    private TrainingProgramRepository $repository;

    public function __construct(
        $programId,
        $branches = ''
    )
    {
        $this->programId = $programId;
        $this->branches = $branches;
        $this->repository = Yii::createObject(TrainingProgramRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        if ($this->branches == '') {
            return $this->repository->prepareResetBranches($this->programId);
        }

        return
            array_merge(
                $this->repository->prepareResetBranches($this->programId),
                $this->repository->prepareConnectBranches($this->programId, $this->branches)
            );
    }
}