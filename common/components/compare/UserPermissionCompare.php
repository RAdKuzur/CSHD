<?php

namespace common\components\compare;

use frontend\models\work\rubac\UserPermissionFunctionWork;
use InvalidArgumentException;

class UserPermissionCompare extends AbstractCompare
{
    public static function compare($c1, $c2): int
    {
        /** @var UserPermissionFunctionWork $c1 */
        /** @var UserPermissionFunctionWork $c2 */
        if (!(get_class($c1) === UserPermissionFunctionWork::class && get_class($c2) === UserPermissionFunctionWork::class)) {
            throw new InvalidArgumentException('Сравниваемые объекты не являются экземплярами класса UserPermissionFunctionWork');
        }

        $result = $c1->function_id <=> $c2->function_id;
        if ($result != 0) {
            return $result;
        }

        return $c1->user_id <=> $c2->user_id;
    }
}