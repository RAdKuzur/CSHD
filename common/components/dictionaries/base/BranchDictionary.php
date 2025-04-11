<?php

namespace common\components\dictionaries\base;

class BranchDictionary extends BaseDictionary
{
    const QUANTORIUM = 1;
    const TECHNOPARK = 2;
    const CDNTT = 3;
    const MOBILE_QUANTUM = 4;
    const ADMINISTRATION = 5;
    const COD = 7;
    const PLANETARIUM = 8;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::TECHNOPARK => 'Технопарк',
            self::QUANTORIUM => 'Кванториум',
            self::CDNTT => 'ЦДНТТ',
            self::COD => 'Центр одаренных детей',
            self::MOBILE_QUANTUM => 'Мобильный Кванториум',
            self::PLANETARIUM => 'Планетарий',
            self::ADMINISTRATION => 'Администрация',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::TECHNOPARK],
            $this->list[self::QUANTORIUM],
            $this->list[self::CDNTT],
            $this->list[self::COD],
            $this->list[self::MOBILE_QUANTUM],
            $this->list[self::PLANETARIUM],
            $this->list[self::ADMINISTRATION],
        ];
    }

    public function getOnlyEducational()
    {
        return [
            self::TECHNOPARK => 'Технопарк',
            self::QUANTORIUM => 'Кванториум',
            self::CDNTT => 'ЦДНТТ',
            self::COD => 'Центр одаренных детей',
            self::MOBILE_QUANTUM => 'Мобильный Кванториум',
        ];
    }

    public static function getByName($name){
        switch ($name){
            case "Технопарк":
                $id = 1;
                break;
            case "Кванториум":
                $id = 2;
                break;
            case "ЦДНТТ":
                $id = 3;
                break;
            case "Центр одаренных детей":
                $id = 4;
                break;
            case "Мобильный Кванториум":
                $id = 5;
                break;
            case "Планетарий":
                $id = 6;
                break;
            case "Администрация":
                $id = 7;
                break;
            default:
                $id = 0;
        }
        return $id;
    }
}