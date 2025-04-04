<?php

namespace frontend\models\auth;

use yii\base\Model;

class LoginModel extends Model
{
    public $username;
    public $password;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['username', 'password'], 'safe']
        ]);
    }
}