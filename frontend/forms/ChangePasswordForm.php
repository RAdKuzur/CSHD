<?php

namespace frontend\forms;

use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $oldPass;
    public $newPass;

    public function rules()
    {
        return [
            [['oldPass', 'newPass'], 'string']
        ];
    }
}