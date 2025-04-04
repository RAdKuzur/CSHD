<?php

namespace common\components\compare;


use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use InvalidArgumentException;

class GroupExpertCompare extends AbstractCompare
{
    public static function compare($c1, $c2): int
    {
        /** @var TrainingGroupExpertWork $c1 */
        /** @var TrainingGroupExpertWork $c2 */
        if (!(get_class($c1) === TrainingGroupExpertWork::class && get_class($c2) === TrainingGroupExpertWork::class)) {
            throw new InvalidArgumentException('Сравниваемые объекты не являются экземплярами класса TrainingGroupExpertWork');
        }

        $result = $c1->training_group_id <=> $c2->training_group_id;
        if ($result != 0) {
            return $result;
        }

        $result = $c1->expert_id <=> $c2->expert_id;
        if ($result != 0) {
            return $result;
        }

        return $c1->expert_type <=> $c2->expert_type;
    }
}