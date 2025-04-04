<?php

namespace common\components\dictionaries;

use common\components\dictionaries\base\BaseDictionary;
use InvalidArgumentException;
use yii\db\Exception;

class FSM
{
    const OPEN = 0;
    const OPEN_READ = 1;
    const OPEN_WRITE = 2;

    public $state = self::OPEN;

    public function checkAvailable() : bool
    {
        switch ($this->state) {
            case self::OPEN:
            case self::OPEN_READ:
                return true;
            case self::OPEN_WRITE:
                return false;
            default:
                throw new InvalidArgumentException('Неизвестный тип состояния объекта');
        }
    }
}