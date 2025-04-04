<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use Yii;

class AddGroupExpertEvent implements EventInterface
{
    private $groupId;
    private $expertId;
    private $expertType;

    private TrainingGroupExpertRepository $repository;

    public function __construct(
        $groupId,
        $expertId,
        $expertType = TrainingGroupExpertWork::TYPE_EXTERNAL
    )
    {
        $this->groupId = $groupId;
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
            $this->repository->prepareCreate(
                $this->groupId,
                $this->expertId,
                $this->expertType
            )
        ];
    }
}