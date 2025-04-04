<?php

namespace common\components\wizards;

use common\models\work\UserWork;
use common\repositories\general\UserRepository;
use frontend\models\work\general\PeopleWork;
use Yii;
use yii\db\Exception;

class LockWizard
{
    // Время жизни блокировки в секундах
    const LOCK_TTL = 600;

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Устанавливает блокировку для объекта
     *
     * @param string $objectId Идентификатор объекта
     * @param string $objectType Тип объекта
     * @param string $userId Идентификатор пользователя
     * @param int $lockTime Время жизни записи (блокировки объекта)
     * @return bool Возвращает true, если блокировка успешна
     */
    public function lockObject(string $objectId, string $objectType, string $userId, int $lockTime = self::LOCK_TTL)
    {
        if (!Yii::$app->redis->isConnected()) {
            return true;
        }

        $key = $this->getLockKey($objectId, $objectType);
        $keyUserdata = $this->getLockUserdataKey($objectId, $objectType);
        $resultKey = Yii::$app->redis->executeCommand('SET', [$key, $userId, 'EX', $lockTime, 'NX']);

        /** @var UserWork $user */
        $user = $this->userRepository->get($userId);
        $resultUserdata = Yii::$app->redis->executeCommand(
            'SET',
            [
                $keyUserdata,
                $user->akaWork ?
                    $user->akaWork->getFIO(PeopleWork::FIO_FULL) :
                    $user->getFullName(),
                'EX',
                self::LOCK_TTL,
                'NX'
            ]
        );

        return ($resultKey == 'OK' && $resultUserdata == 'OK') ||
            Yii::$app->redis->executeCommand('GET', [$key]) == $userId;
    }

    /**
     * Освобождает блокировку для объекта
     *
     * @param string $objectId Идентификатор объекта
     * @return void
     */
    public function unlockObject(string $objectId, string $objectType)
    {
        if (!Yii::$app->redis->isConnected()) {
            return;
        }

        $key = $this->getLockKey($objectId, $objectType);
        $keyUserdata = $this->getLockUserdataKey($objectId, $objectType);
        Yii::$app->redis->executeCommand('DEL', [$key]);
        Yii::$app->redis->executeCommand('DEL', [$keyUserdata]);
    }

    /**
     * Проверяет, заблокирован ли объект
     *
     * @param string $objectId Идентификатор объекта
     * @return bool
     */
    public function isObjectLocked($objectId, $objectType)
    {
        if (!Yii::$app->redis->isConnected()) {
            return false;
        }

        $key = $this->getLockKey($objectId, $objectType);
        return Yii::$app->redis->executeCommand('EXISTS', [$key]) > 0;
    }

    /**
     * Возвращает данные о пользователе, инициировавшем и удерживающем блокировку
     *
     * @param string $objectId Идентификатор объекта
     * @param string $objectType Тип объекта
     * @return array|bool|string|null
     * @throws \yii\db\Exception
     */
    public function getUserdata($objectId, $objectType)
    {
        if (!Yii::$app->redis->isConnected()) {
            return false;
        }

        $keyUserdata = $this->getLockUserdataKey($objectId, $objectType);
        return Yii::$app->redis->executeCommand('GET', [$keyUserdata]);
    }

    /**
     * Обновляет время жизни блокировки
     *
     * @param string $objectId Идентификатор объекта
     * @param string $objectType Тип объекта
     * @param int $lockTime Время жизни записи (блокировки объекта)
     * @return bool
     * @throws Exception
     */
    public function refreshLock(string $objectId, string $objectType, int $lockTime = self::LOCK_TTL)
    {
        if (!Yii::$app->redis->isConnected()) {
            return true;
        }

        $key = $this->getLockKey($objectId, $objectType);
        if ($this->isObjectLocked($objectId, $objectType)) {
            return Yii::$app->redis->executeCommand('EXPIRE', [$key, $lockTime]);
        }

        return false;
    }

    /**
     * Получает ключ блокировки
     *
     * @param string $objectId Идентификатор объекта
     * @param string $objectType Тип объекта
     * @return string
     */
    private function getLockKey(string $objectId, string $objectType)
    {
        return "lock:{$objectType}:{$objectId}";
    }

    /**
     * Получает ключ для данных о пользователе, инициирующего блокировку
     *
     * @param string $objectId Идентификатор объекта
     * @param string $objectType Тип объекта
     * @return string
     */
    private function getLockUserdataKey(string $objectId, string $objectType)
    {
        return "lock_userdata:{$objectType}:{$objectId}";
    }

    /**
     * !НЕ ИСПОЛЬЗОВАТЬ. ТОЛЬКО ДЛЯ ЭКСТРЕННЫХ СЛУЧАЕВ!
     * Сбрасывает все блокировки объектов
     *
     * @return void
     */
    public function resetAllLocks()
    {
        if (!Yii::$app->redis->isConnected()) {
            return;
        }

        $patternLock = "lock:*";
        $patternUserdata = "lock_userdata:*";

        $this->deleteKeysByPattern($patternLock);
        $this->deleteKeysByPattern($patternUserdata);
    }

    /**
     * Удаляет записи по паттерну ключа
     *
     * @param string $pattern паттерн/маска
     * @param int $count количество записей, которые необходимо просканировать
     * @return void
     * @throws Exception
     */
    private function deleteKeysByPattern(string $pattern, int $count = 10000)
    {
        if (!Yii::$app->redis->isConnected()) {
            return;
        }

        $iterator = null;
        do {
            $keys = Yii::$app->redis->executeCommand('SCAN', [$iterator, 'MATCH', $pattern, 'COUNT', $count]);
            $iterator = $keys[0];
            $matchingKeys = $keys[1];

            // Удаление найденных ключей
            if (!empty($matchingKeys)) {
                Yii::$app->redis->executeCommand('DEL', $matchingKeys);
            }
        } while ($iterator > 0);
    }
}