<?php

namespace frontend\models\work\rubac;

use common\models\scaffold\PermissionToken;
use common\models\work\UserWork;

/**
 * @property UserWork $userWork
 * @property PermissionFunctionWork $permissionWork
 */
class PermissionTokenWork extends PermissionToken
{
    public static function fill(
        int $userId,
        int $permissionId,
        string $startTime,
        string $endTime,
        int $branch = null
    )
    {
        $entity = new static();
        $entity->user_id = $userId;
        $entity->function_id = $permissionId;
        $entity->branch = $branch;
        $entity->start_time = $startTime;
        $entity->end_time = $endTime;

        return $entity;
    }

    public function getUserWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'user_id']);
    }

    public function getPermissionWork()
    {
        return $this->hasOne(PermissionFunctionWork::class, ['id' => 'function_id']);
    }
}
