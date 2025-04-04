<?php

namespace common\components\dictionaries\base;

class EventTypeDictionary extends BaseDictionary
{
    const COMPETITIVE = 1;
    const NON_COMPETITIVE = 2;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::COMPETITIVE => 'Соревновательный',
            self::NON_COMPETITIVE => 'Несоревновательный',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::COMPETITIVE],
            $this->list[self::NON_COMPETITIVE],
        ];
    }
}