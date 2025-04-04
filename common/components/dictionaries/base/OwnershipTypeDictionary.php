<?php

namespace common\components\dictionaries\base;

class OwnershipTypeDictionary extends BaseDictionary
{
    const BUDGET = 1;
    const AUTONOMOUS = 2;
    const GOVERNMENT = 3;
    const UNITARY = 4;
    const NON_COMMERCIAL = 5;
    const ATYPICAL = 6;
    const OOO = 7;
    const INDIVIDUAL = 8;
    const PAO = 9;
    const AO = 10;
    const ZAO = 11;
    const PHYSICAL = 12;
    const OTHER = 13;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::BUDGET => 'Бюджетное',
            self::AUTONOMOUS => 'Автономное',
            self::GOVERNMENT => 'Казенное',
            self::UNITARY => 'Унитарное',
            self::NON_COMMERCIAL => 'НКО',
            self::ATYPICAL => 'Нетиповое',
            self::OOO => 'ООО',
            self::INDIVIDUAL => 'ИП',
            self::PAO => 'ПАО',
            self::AO => 'АО',
            self::ZAO => 'ЗАО',
            self::PHYSICAL => 'Физлицо',
            self::OTHER => 'Прочее',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::BUDGET],
            $this->list[self::AUTONOMOUS],
            $this->list[self::GOVERNMENT],
            $this->list[self::UNITARY],
            $this->list[self::NON_COMMERCIAL],
            $this->list[self::ATYPICAL],
            $this->list[self::OOO],
            $this->list[self::INDIVIDUAL],
            $this->list[self::PAO],
            $this->list[self::AO],
            $this->list[self::ZAO],
            $this->list[self::PHYSICAL],
            $this->list[self::OTHER],
        ];
    }
}