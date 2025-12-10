<?php

namespace common\components\dictionaries\base;

class EventWayDictionary extends BaseDictionary
{
    const PERSONAL_PRESENCE = 1;
    const PERSONAL_REMOTE = 2;
    const ABSENTEE = 3;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::PERSONAL_PRESENCE => 'Очный (явка)',
            self::PERSONAL_REMOTE => 'Очный (дистанционно)',
            self::ABSENTEE => 'Заочный',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::PERSONAL_PRESENCE],
            $this->list[self::PERSONAL_REMOTE],
            $this->list[self::ABSENTEE],
        ];
    }
    //----------------------Добавлено------------------
    public static function getByName($name){
        switch ($name){
            case "Очный (явка)":
                $id = 1;
                break;
            case "Очный (дистанционно)":
                $id = 2;
                break;
            case "Заочный":
                $id = 3;
                break;
            default:
                $id = 0;
        }
        return $id;
    }
}