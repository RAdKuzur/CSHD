<?php

namespace frontend\events\visit;

use common\events\EventInterface;
use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\services\educational\JournalService;
use Yii;
use yii\helpers\ArrayHelper;

class AddLessonToVisitEvent implements EventInterface
{
    private JournalService $service;
    private VisitRepository $repository;
    private $groupId;

    /** @var TrainingGroupLessonWork[] $lessons */
    private array $lessons;

    public function __construct(
        $groupId,
        array $lessons
    )
    {
        $this->groupId = $groupId;
        $this->lessons = $lessons;
        $this->service = Yii::createObject(JournalService::class);
        $this->repository = Yii::createObject(VisitRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        $newLessonsString = $this->service->createLessonString($this->groupId, $this->lessons, []);
        $trainingGroupIds = ArrayHelper::getColumn($this->repository->getByTrainingGroup($this->groupId), 'id');

        return [
            $this->repository->prepareUpdateLessons($trainingGroupIds, $newLessonsString)
        ];
    }
}