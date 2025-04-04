<?php

namespace frontend\models\work\educational\journal;

use common\models\scaffold\Visit;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;

/**
 * @property TrainingGroupParticipantWork $trainingGroupParticipantWork
 */
class VisitWork extends Visit
{
    const ATTENDANCE = 0;
    const NO_ATTENDANCE = 1;
    const DISTANCE = 2;
    const NONE = 3;

    /** @var VisitLesson[] $visitLessons */
    public array $visitLessons;

    public static function fill(int $groupParticipantId, string $lessons = '')
    {
        $entity = new static();
        $entity->training_group_participant_id = $groupParticipantId;
        $entity->lessons = $lessons;

        return $entity;
    }

    public function fillLessons()
    {
        $lessonsArray = array_map(function ($lesson) {
            return [
                'lesson_id' => $lesson->lessonId,
                'status' => $lesson->status,
            ];
        }, $this->visitLessons);

        $this->lessons = json_encode($lessonsArray);
    }

    public function getTrainingGroupParticipantWork()
    {
        return $this->hasOne(TrainingGroupParticipantWork::class, ['id' => 'training_group_participant_id']);
    }

    public function setLessons(string $lessons)
    {
        $this->lessons = $lessons;
    }

}
