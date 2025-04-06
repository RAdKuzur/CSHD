<?php

namespace common\repositories\providers\user;

use common\models\work\UserWork;
use DomainException;

class UserProvider implements UserProviderInterface
{
    public function get($id)
    {
        return UserWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return UserWork::find()->all();
    }

    public function getByUsername($username)
    {
        return UserWork::find()->where(['username' => $username])->one();
    }

    public function getByEmail($email)
    {
        return UserWork::find()->where(['email' => $email])->one();
    }

    public function save(UserWork $user)
    {
        if (!$user->save()) {
            throw new DomainException('Ошибка сохранения пользователя. Проблемы: '.json_encode($user->getErrors()));
        }

        return $user->id;
    }
}