<?php

namespace frontend\models\auth;

use yii\db\ActiveRecord;

class ForgotPassword extends ActiveRecord
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'email'],
        ];
    }
}