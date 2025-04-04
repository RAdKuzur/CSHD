<?php

namespace common\repositories\general;

use common\components\traits\CommonDatabaseFunctions;
use common\models\work\UserWork;
use common\repositories\providers\user\UserProvider;
use common\repositories\providers\user\UserProviderInterface;
use Yii;

class UserRepository
{
    use CommonDatabaseFunctions;

    private $provider;

    public function __construct(UserProviderInterface $provider = null)
    {
        if (!$provider) {
            $provider = Yii::createObject(UserProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getAll()
    {
        return $this->provider->getAll();
    }

    public function findByUsername($username)
    {
        return $this->provider->getByUsername($username);
    }

    public function findByEmail($email)
    {
        return $this->provider->getByEmail($email);
    }

    public function changePassword($password, $userId)
    {
        $passwordHash = Yii::$app->security->generatePasswordHash($password);
        /** @var UserWork $user */
        $user = $this->get($userId);

        if ($user) {
            $user->setPassword($passwordHash);
            $this->save($user);
        }
    }

    public function save(UserWork $user)
    {
        return $this->provider->save($user);
    }

    public function delete(UserWork $model)
    {
        return $model->delete();
    }
}