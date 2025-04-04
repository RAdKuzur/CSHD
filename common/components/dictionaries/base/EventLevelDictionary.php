<?php

namespace common\components\dictionaries\base;

class EventLevelDictionary extends BaseDictionary
{
    const INTERIOR = 3;
    const DISTRICT = 4;
    const URBAN = 5;
    const REGIONAL = 6;
    const FEDERAL = 7;
    const INTERNATIONAL = 8;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::INTERIOR => 'Внутренний',
            self::DISTRICT => 'Районный',
            self::URBAN => 'Городской',
            self::REGIONAL => 'Региональный',
            self::FEDERAL => 'Федеральный',
            self::INTERNATIONAL => 'Международный',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::INTERIOR],
            $this->list[self::DISTRICT],
            $this->list[self::URBAN],
            $this->list[self::REGIONAL],
            $this->list[self::FEDERAL],
            $this->list[self::INTERNATIONAL],
        ];
    }

    public function getReportLevels()
    {
        return [
            self::REGIONAL,
            self::FEDERAL,
            self::INTERNATIONAL
        ];
    }
}