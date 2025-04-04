<?php

namespace common\components\dictionaries\base;

class FocusDictionary extends BaseDictionary
{
    const TECHNICAL = 1;
    const ART = 2;
    const SOCIAL = 3;
    const SCIENCE = 4;
    const SPORT = 5;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::TECHNICAL => 'Техническая',
            self::ART => 'Художественная',
            self::SOCIAL => 'Социально-педагогическая',
            self::SCIENCE => 'Естественнонаучная',
            self::SPORT => 'Физкультурно-спортивная',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::TECHNICAL],
            $this->list[self::ART],
            $this->list[self::SOCIAL],
            $this->list[self::SCIENCE],
            $this->list[self::SPORT],
        ];
    }
}