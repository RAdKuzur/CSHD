<?php

namespace common\components\dictionaries\base;

class DocumentTypeDictionary extends BaseDictionary
{
    const TYPE_ORDER = 1;
    const TYPE_OUT = 2;
    const TYPE_IN = 3;
    const TYPE_REGULATION = 4;
    const TYPE_EVENT_REGULATION = 5;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::TYPE_ORDER => 'Приказ',
            self::TYPE_OUT => 'Исходящий документ',
            self::TYPE_IN => 'Входящий документ',
            self::TYPE_REGULATION => 'Положение, инструкция или правило',
            self::TYPE_EVENT_REGULATION => 'Положение о мероприятии',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::TYPE_ORDER],
            $this->list[self::TYPE_OUT],
            $this->list[self::TYPE_IN],
            $this->list[self::TYPE_REGULATION],
            $this->list[self::TYPE_EVENT_REGULATION],
        ];
    }
}