<?php

namespace common\repositories\providers\user;

use common\models\work\UserWork;

interface UserProviderInterface
{
    public function get($id);
    public function getAll();
    public function getByUsername($username);
    public function getByEmail($email);
    public function save(UserWork $user);
}