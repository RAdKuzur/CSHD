<?php

namespace frontend\events\educational\training_program;

use common\events\EventInterface;
use common\repositories\educational\TrainingProgramRepository;
use Yii;

class CreateThemeInPlanEvent implements EventInterface
{
    private $theme;
    private $programId;
    private $controlType;

    private TrainingProgramRepository $repository;

    public function __construct(
        $theme,
        $programId,
        $controlType
    )
    {
        $this->theme = $theme;
        $this->programId = $programId;
        $this->controlType = $controlType;
        $this->repository = Yii::createObject(TrainingProgramRepository ::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareCreateTheme($this->theme, $this->programId, $this->controlType)
        ];
    }
}