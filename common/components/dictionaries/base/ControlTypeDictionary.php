<?php

namespace common\components\dictionaries\base;

class ControlTypeDictionary extends BaseDictionary
{
    const CONTROL_WORK = 1;
    const BLITZ_SURVEY = 2;
    const EDU_VISION = 3;
    const SURVEY = 4;
    const COMPLETE_WORK = 5;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::CONTROL_WORK => 'Контрольная работа',
            self::BLITZ_SURVEY => 'Блиц-опрос',
            self::EDU_VISION => 'Педагогическое наблюдение',
            self::SURVEY => 'Опрос',
            self::COMPLETE_WORK => 'Выполненная работа',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::CONTROL_WORK],
            $this->list[self::BLITZ_SURVEY],
            $this->list[self::EDU_VISION],
            $this->list[self::SURVEY],
            $this->list[self::COMPLETE_WORK],
        ];
    }
}