<?php

namespace common\components\dictionaries\base;

class CompanyTypeDictionary extends BaseDictionary
{
    const EDUCATIONAL = 1;
    const GOVERNMENT = 2;
    const INDIVIDUAL = 3;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::EDUCATIONAL => 'Образовательная учреждение',
            self::GOVERNMENT => 'Государственное учреждение',
            self::INDIVIDUAL => 'Частная организация / ИП',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::EDUCATIONAL],
            $this->list[self::GOVERNMENT],
            $this->list[self::INDIVIDUAL],
        ];
    }
}