<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\educational\ProjectThemeRepository;
use Yii;

class UpdateProjectThemeEvent implements EventInterface
{
    private $id;
    private $projectType;
    private $description;

    private ProjectThemeRepository $repository;

    public function __construct(
        $id,
        $projectType,
        $description
    )
    {
        $this->id = $id;
        $this->projectType = $projectType;
        $this->description = $description;
        $this->repository = Yii::createObject(ProjectThemeRepository::class);
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
                $this->projectType,
                $this->description
            )
        ];
    }
}