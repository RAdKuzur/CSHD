<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use Yii;

class CreateTrainingGroupLessonEvent implements EventInterface
{
    private $groupId;
    private $lessonDate;
    private $lessonStartTime;
    private $branch;
    private $auditoriumId;
    private $lessonEndTime;
    private $duration;

    private TrainingGroupLessonRepository $repository;

    public function __construct(
        $groupId,
        $lessonDate,
        $lessonStartTime,
        $branch,
        $auditoriumId,
        $lessonEndTime,
        $duration
    )
    {
        $this->groupId = $groupId;
        $this->lessonDate = $lessonDate;
        $this->lessonStartTime = $lessonStartTime;
        $this->branch = $branch;
        $this->auditoriumId = $auditoriumId;
        $this->lessonEndTime = $lessonEndTime;
        $this->duration = $duration;
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
                $this->groupId,
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