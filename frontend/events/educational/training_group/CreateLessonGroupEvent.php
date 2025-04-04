<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use Yii;

class CreateLessonGroupEvent implements EventInterface
{
    private $lessonDate;
    private $lessonStartTime;
    private $lessonEndTime;
    private $duration;
    private $branch;
    private $auditoriumId;
    private $trainingGroupId;

    private TrainingGroupLessonRepository $repository;

    public function __construct(
        $lessonDate,
        $lessonStartTime,
        $lessonEndTime,
        $duration,
        $branch,
        $auditoriumId,
        $trainingGroupId
    )
    {
        $this->lessonDate = $lessonDate;
        $this->lessonStartTime = $lessonStartTime;
        $this->lessonEndTime = $lessonEndTime;
        $this->duration = $duration;
        $this->branch = $branch;
        $this->auditoriumId = $auditoriumId;
        $this->trainingGroupId = $trainingGroupId;
        $this->repository = Yii::createObject(TrainingGroupLessonRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareCreate(
                $this->trainingGroupId,
                $this->lessonDate,
                $this->lessonStartTime,
                $this->branch,
                $this->auditoriumId,
                $this->lessonEndTime,
                $this->duration
            )
        ];
    }
}