<?php

namespace common\components\access;

use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\rubac\PermissionFunctionWork;
use Yii;

class AuthDataCache
{
    /*
     * Формат данных:
     * Redis SETS
     * key: value1, value2, ...
     * user_id: function_id_1, function_id_2, ...
     */

    const CACHE_LIFETIME = 28800;
    private UserPermissionFunctionRepository $userFunctionRepository;

    public function __construct(UserPermissionFunctionRepository $userFunctionRepository)
    {
        $this->userFunctionRepository = $userFunctionRepository;
    }

    /**
     * Загрузка данных в Redis по id пользователя
     *
     * @param int $userId id пользователя системы
     * @return void|bool
     * @throws \yii\db\Exception
     */
    public function loadDataFromDB(int $userId)
    {
        if (!Yii::$app->redis->isConnected()) {
            return false;
        }

        // Проверка на существование данных о пользователей в кэше
        $key = $this->getAuthSetKey($userId);
        if (Yii::$app->redis->executeCommand('EXISTS', [$key])) {
            return false;
        }

        $permissions = $this->userFunctionRepository->getPermissionsByUser($userId);
        $this->loadDataFromPermissions($permissions, $userId);
    }

    /**
     * Загрузка данных в Redis из готового массива правил
     *
     * @param array $permissions массив правил @see PermissionFunctionWork
     * @param int $userId
     * @return void
     * @throws \yii\db\Exception
     */
    public function loadDataFromPermissions(array $permissions, int $userId)
    {
        if (!Yii::$app->redis->isConnected()) {
            return;
        }

        // Проверка на существование данных о пользователей в кэше
        $key = $this->getAuthSetKey($userId);
        if (Yii::$app->redis->executeCommand('EXISTS', [$key])) {
            return;
        }

        $transactionFlag = true;

        foreach ($permissions as $permission) {
            /** @var PermissionFunctionWork $permission */
            $result = Yii::$app->redis->executeCommand('SADD', [$key, $permission->short_code]);
            if ($result == 0) {
                $transactionFlag = false;
            }
        }

        if (!$transactionFlag) {
            Yii::$app->redis->executeCommand('DEL', $key);
        }
        else {
            Yii::$app->redis->executeCommand('EXPIRE', [$key, self::CACHE_LIFETIME]);
        }
    }

    public function getAllPermissionsFromUser($userId)
    {
        if (!Yii::$app->redis->isConnected()) {
            return false;
        }

        $key = $this->getAuthSetKey($userId);
        return Yii::$app->redis->executeCommand('SMEMBERS', [$key]);
    }

    /**
     * Проверка на наличие правила accessId у пользователя userId
     *
     * @param int $accessId id правила @see PermissionFunctionWork
     * @param int $userId id пользователя системы
     * @return array|bool|string|null
     * @throws \yii\db\Exception
     */
    public function checkAccessForUser(int $accessId, int $userId)
    {
        if (!Yii::$app->redis->isConnected()) {
            return false;
        }

        $key = $this->getAuthSetKey($userId);
        return Yii::$app->redis->executeCommand('SISMEMBER', [$key, $accessId]);
    }

    /**
     * Очистка правил по конкретному пользователю
     *
     * @param int $userId id пользователя системы
     * @return array|bool|string|null
     * @throws \yii\db\Exception
     */
    public function clearAuthData(int $userId)
    {
        if (!Yii::$app->redis->isConnected()) {
            return false;
        }

        $key = $this->getAuthSetKey($userId);
        return Yii::$app->redis->executeCommand('DEL', [$key]);
    }

    private function getAuthSetKey($userId)
    {
        return "user:permissions:{$userId}";
    }
}