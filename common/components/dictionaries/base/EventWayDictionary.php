<?php

namespace common\components\dictionaries\base;

class EventWayDictionary extends BaseDictionary
{
    const PERSONAL_PRESENCE = 1;
    const PERSONAL_REMOTE = 2;
    const ABSENTEE = 3;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::PERSONAL_PRESENCE => 'Очный (явка)',
            self::PERSONAL_REMOTE => 'Очный (дистанционно)',
            self::ABSENTEE => 'Заочный',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::PERSONAL_PRESENCE],
            $this->list[self::PERSONAL_REMOTE],
            $this->list[self::ABSENTEE],
        ];
    }
}