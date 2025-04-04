<?php

namespace common\components\compare;


use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use InvalidArgumentException;

class LessonGroupCompare extends AbstractCompare
{
    public static function compare($c1, $c2): int
    {
        /** @var TrainingGroupLessonWork $c1 */
        /** @var TrainingGroupLessonWork $c2 */
        if (!(get_class($c1) === TrainingGroupLessonWork::class && get_class($c2) === TrainingGroupLessonWork::class)) {
            throw new InvalidArgumentException('Сравниваемые объекты не являются экземплярами класса TrainingGroupParticipantWork');
        }

        $result = $c1->branch <=> $c2->branch;
        if ($result != 0) {
            return $result;
        }

        $result = $c1->auditorium_id <=> $c2->auditorium_id;
        if ($result != 0) {
            return $result;
        }

        $result = $c1->lesson_date <=> $c2->lesson_date;
        if ($result != 0) {
            return $result;
        }

        return $c1->lesson_start_time <=> $c2->lesson_start_time;
    }
}