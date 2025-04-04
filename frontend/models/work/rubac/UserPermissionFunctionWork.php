<?php

namespace frontend\models\work\rubac;

use common\models\scaffold\UserPermissionFunction;

class UserPermissionFunctionWork extends UserPermissionFunction
{
    public static function fill(
        int $userId,
        int $functionId,
        int $branch = null
    )
    {
        $entity = new static();
        $entity->user_id = $userId;
        $entity->function_id = $functionId;
        $entity->branch = $branch;

        return $entity;
    }
}
