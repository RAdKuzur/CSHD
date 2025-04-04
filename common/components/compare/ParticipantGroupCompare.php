<?php

namespace common\components\compare;


use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use InvalidArgumentException;

class ParticipantGroupCompare extends AbstractCompare
{
    public static function compare($c1, $c2): int
    {
        /** @var TrainingGroupParticipantWork $c1 */
        /** @var TrainingGroupParticipantWork $c2 */
        if (!(get_class($c1) === TrainingGroupParticipantWork::class && get_class($c2) === TrainingGroupParticipantWork::class)) {
            throw new InvalidArgumentException('Сравниваемые объекты не являются экземплярами класса TrainingGroupParticipantWork');
        }

        $result = $c1->training_group_id <=> $c2->training_group_id;
        if ($result != 0) {
            return $result;
        }

        return $c1->participant_id <=> $c2->participant_id;
    }
}