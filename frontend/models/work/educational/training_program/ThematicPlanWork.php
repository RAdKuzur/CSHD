<?php

namespace frontend\models\work\educational\training_program;

use common\models\scaffold\ThematicPlan;
use InvalidArgumentException;
use Yii;

class ThematicPlanWork extends ThematicPlan
{
    public static function fill($theme, $programId, $controlType)
    {
        if (!array_key_exists($controlType, Yii::$app->controlType->getList())) {
            throw new InvalidArgumentException('Неизвестный тип контроля: ' . $controlType);
        }

        $entity = new static();
        $entity->theme = $theme;
        $entity->training_program_id = $programId;
        $entity->control_type = $controlType;

        return $entity;
    }
}