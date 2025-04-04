<?php

namespace common\repositories\providers\user;

use common\models\work\UserWork;

class UserMockProvider implements UserProviderInterface
{
    private $users = [];

    public function get($id)
    {
        return $this->users[$id] ?? null;
    }

    public function getAll()
    {
        return array_values($this->users);
    }

    public function getByUsername($username)
    {
        foreach ($this->users as $user) {
            if ($user->username === $username) {
                return $user;
            }
        }
        return null;
    }

    public function getByEmail($email)
    {
        foreach ($this->users as $user) {
            if ($user->email === $email) {
                return $user;
            }
        }
        return null;
    }

    public function save(UserWork $user)
    {
        $this->addUser($user);
        return $user->id;
    }

    public function addUser(UserWork $user)
    {
        $this->users[$user->id] = $user;
    }
}