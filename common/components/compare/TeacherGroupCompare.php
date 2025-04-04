<?php

namespace common\components\compare;


use frontend\models\work\educational\training_group\TeacherGroupWork;
use InvalidArgumentException;

class TeacherGroupCompare extends AbstractCompare
{
    public static function compare($c1, $c2): int
    {
        /** @var TeacherGroupWork $c1 */
        /** @var TeacherGroupWork $c2 */
        if (!(get_class($c1) === TeacherGroupWork::class && get_class($c2) === TeacherGroupWork::class)) {
            throw new InvalidArgumentException('Сравниваемые объекты не являются экземплярами класса TeacherGroupWork');
        }

        $result = $c1->training_group_id <=> $c2->training_group_id;
        if ($result != 0) {
            return $result;
        }

        return $c1->teacher_id <=> $c2->teacher_id;
    }
}