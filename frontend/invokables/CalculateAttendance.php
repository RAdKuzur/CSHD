<?php

namespace frontend\invokables;

use common\repositories\educational\TrainingGroupLessonRepository;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use Yii;

class CalculateAttendance
{
    /** @var VisitWork[] $visits */
    private array $visits;
    private TrainingGroupLessonRepository $lessonRepository;

    public function __construct(
        array $visits,
        TrainingGroupLessonRepository $lessonRepository = null
    )
    {
        if (is_null($lessonRepository)) {
            $lessonRepository = (Yii::createObject(TrainingGroupLessonRepository::class));
        }

        $this->lessonRepository = $lessonRepository;
        $this->visits = $visits;
    }

    public function __invoke() : int
    {
        $result = 0;
        foreach ($this->visits as $visit) {
            $lessons = VisitLesson::fromString($visit->lessons, $this->lessonRepository);
            foreach ($lessons as $lesson) {
                if ($lesson->isPresence()) {
                    $result++;
                }
            }
        }

        return $result;
    }
}