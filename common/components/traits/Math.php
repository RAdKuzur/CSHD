<?php

namespace common\components\traits;

use common\components\compare\AbstractCompare;
use InvalidArgumentException;
use Yii;

trait Math
{
    public function setDifference(array $set1, array $set2, string $compareClassname)
    {
        $compareModel = Yii::createObject($compareClassname);
        if (!($compareModel instanceof AbstractCompare)) {
            throw new InvalidArgumentException("$compareModel не является наследником AbstractCompare");
        }

        if (empty($set1)) {
            return [];
        } elseif (empty($set2)) {
            return $set1;
        }

        return array_udiff($set1, $set2, [$compareModel, 'compare']);
    }

    public function percent($numb1, $numb2, int $precision = 0)
    {
        if ($numb1 == 0 || $numb2 == 0) {
            return 0;
        }

        return round(($numb1 / $numb2) * 100, $precision);
    }

}