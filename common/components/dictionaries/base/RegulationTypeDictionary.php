<?php

namespace common\components\dictionaries\base;

class RegulationTypeDictionary extends BaseDictionary
{
    const TYPE_REGULATION = 1;
    const TYPE_EVENT = 2;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::TYPE_REGULATION => 'Положения, инструкции, правила',
            self::TYPE_EVENT => 'Положения о мероприятиях',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::TYPE_REGULATION],
            $this->list[self::TYPE_EVENT],
        ];
    }
}