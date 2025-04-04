<?php

namespace frontend\events\educational\training_program;

use common\events\EventInterface;
use common\repositories\educational\TrainingProgramRepository;
use frontend\models\work\educational\training_program\ThematicPlanWork;
use Yii;

class ResetThematicPlanEvent implements EventInterface
{
    private $programId;

    private TrainingProgramRepository $repository;

    public function __construct(
        $programId
    )
    {
        $this->programId = $programId;
        $this->repository = Yii::createObject(TrainingProgramRepository ::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        $themes = $this->repository->getThematicPlan($this->programId);
        $result = [];
        foreach ($themes as $theme) {
            /** @var ThematicPlanWork $theme */
            $result[] = $this->repository->prepareDeleteTheme($theme->id);
        }

        return $result;
    }
}