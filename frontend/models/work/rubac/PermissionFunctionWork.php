<?php

namespace frontend\models\work\rubac;

use common\models\scaffold\PermissionFunction;

class PermissionFunctionWork extends PermissionFunction
{
    const PERMISSION_THEIR_GROUPS_ID = 2;
    const PERMISSION_BRANCH_GROUPS_ID = 3;
    const PERMISSION_ALL_GROUPS_ID = 4;

    public static function fill($name, $shortCode, $id = null)
    {
        $entity = new static();
        if ($id) {
            $entity->id = $id;
        }
        $entity->name = $name;
        $entity->short_code = $shortCode;

        return $entity;
    }
}
