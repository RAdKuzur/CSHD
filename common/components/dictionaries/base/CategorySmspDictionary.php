<?php

namespace common\components\dictionaries\base;

class CategorySmspDictionary extends BaseDictionary
{
    const MICRO_ENTERPRISE = 1;
    const SMALL_ENTERPRISE = 2;
    const MEDIUM_ENTERPRISE = 3;
    const SELF_EMPLOYED = 4;
    const NOT_SMSP = 5;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::MICRO_ENTERPRISE => 'Микропредприятие',
            self::SMALL_ENTERPRISE => 'Малое предприятие',
            self::MEDIUM_ENTERPRISE => 'Среднее предприятие',
            self::SELF_EMPLOYED => 'Самозанятый',
            self::NOT_SMSP => 'Не СМСП',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::MICRO_ENTERPRISE],
            $this->list[self::SMALL_ENTERPRISE],
            $this->list[self::MEDIUM_ENTERPRISE],
            $this->list[self::SELF_EMPLOYED],
            $this->list[self::NOT_SMSP],
        ];
    }
}