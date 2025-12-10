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
    //----------------------Добавлено------------------
    public static function getByName($name){
        switch ($name){
            case "Техническая":
                $id = 1;
                break;
            case "Художественная":
                $id = 2;
                break;
            case "Социально-педагогическая":
                $id = 3;
                break;
            case "Естественнонаучная":
                $id = 4;
                break;
            case "Физкультурно-спортивная":
                $id = 5;
                break;
            default:
                $id = 0;
        }
        return $id;
    }
}