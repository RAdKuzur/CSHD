<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use Yii;

class UpdateGroupExpertEvent implements EventInterface
{
    private $id;
    private $expertId;
    private $expertType;

    private TrainingGroupExpertRepository $repository;

    public function __construct(
        $id,
        $expertId,
        $expertType
    )
    {
        $this->id = $id;
        $this->expertId = $expertId;
        $this->expertType = $expertType;
        $this->repository = Yii::createObject(TrainingGroupExpertRepository::class);
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
                $this->expertId,
                $this->expertType
            )
        ];
    }
}