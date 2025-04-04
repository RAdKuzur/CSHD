<?php

namespace common\components\compare;


use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use InvalidArgumentException;

class GroupThemeCompare extends AbstractCompare
{
    public static function compare($c1, $c2): int
    {
        /** @var GroupProjectThemesWork $c1 */
        /** @var GroupProjectThemesWork $c2 */
        if (!(get_class($c1) === GroupProjectThemesWork::class && get_class($c2) === GroupProjectThemesWork::class)) {
            throw new InvalidArgumentException('Сравниваемые объекты не являются экземплярами класса GroupProjectThemesWork');
        }

        $result = $c1->training_group_id <=> $c2->training_group_id;
        if ($result != 0) {
            return $result;
        }

        return $c1->project_theme_id <=> $c2->project_theme_id;
    }
}