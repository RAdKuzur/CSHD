<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use Yii;

class DeleteGroupExpertEvent implements EventInterface
{
    private $id;

    private TrainingGroupExpertRepository $repository;

    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->repository = Yii::createObject(TrainingGroupExpertRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareDelete(
                $this->id
            )
        ];
    }
}