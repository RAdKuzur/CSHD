<?php

namespace common\components\dictionaries\base;

class ProjectTypeDictionary extends BaseDictionary
{
    const TECHNICAL = 1;
    const RESEARCH = 2;
    const CREATIVE = 2;
    const MEDIA = 4;
    const JOURNALISTIC = 5;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::TECHNICAL => 'Научно-технический',
            self::RESEARCH => 'Исследовательский',
            self::CREATIVE => 'Творческий',
            self::MEDIA => 'Медиа',
            self::JOURNALISTIC => 'Журналистский',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::TECHNICAL],
            $this->list[self::RESEARCH],
            $this->list[self::CREATIVE],
            $this->list[self::MEDIA],
            $this->list[self::JOURNALISTIC],
        ];
    }
}