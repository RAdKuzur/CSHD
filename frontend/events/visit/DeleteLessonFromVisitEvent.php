<?php

namespace frontend\events\visit;

use common\events\EventInterface;
use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\services\educational\JournalService;
use Yii;
use yii\helpers\ArrayHelper;

class DeleteLessonFromVisitEvent implements EventInterface
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
        $updates = [];
        $visits = $this->repository->getByTrainingGroup($this->groupId);
        foreach ($visits as $visit) {
            $curLessonsString = $visit->lessons;
            $newLessonsJson = $this->service->createLessonString($curLessonsString, [], $this->lessons);
            $updates[$visit->id] = $newLessonsJson;
        }


        return [
            $this->repository->prepareBatchUpdateLessons($updates)
        ];
    }
}