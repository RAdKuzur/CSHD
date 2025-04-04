<?php

namespace frontend\forms;

use DateTime;
use yii\base\Model;

class TokenForm extends Model
{
    public $user;
    public $permission;
    public $week;
    public $day;
    public $hour;
    public function rules() {
        return [
            [['user', 'permission'], 'required'],
            [['week', 'day', 'hour'], 'integer']
        ];
    }
    public function date(DateTime $date){
        if ($this->week != '') {
            $date->modify("+ $this->week week");
        }
        if ($this->day != '') {
            $date->modify("+ $this->day day");
        }
        if ($this->hour != '') {
            $date->modify("+ $this->hour hour");
        }
        return $date;
    }
}