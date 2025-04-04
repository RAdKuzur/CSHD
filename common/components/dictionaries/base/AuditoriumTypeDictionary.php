<?php

namespace common\components\dictionaries\base;

class AuditoriumTypeDictionary extends BaseDictionary
{
    const LABORATORY = 1;
    const WORKSHOP = 2;
    const STUDY_CLASS = 3;
    const LECTURE_HALL = 4;
    const COMPUTER_ROOM = 5;
    const ASSEMBLY_HALL = 6;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::LABORATORY => 'Лаборатория',
            self::WORKSHOP => 'Мастерская',
            self::STUDY_CLASS => 'Учебный класс',
            self::LECTURE_HALL => 'Лекционная аудитория',
            self::COMPUTER_ROOM => 'Компьютерный кабинет',
            self::ASSEMBLY_HALL => 'Актовый зал',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::LABORATORY],
            $this->list[self::WORKSHOP],
            $this->list[self::STUDY_CLASS],
            $this->list[self::LECTURE_HALL],
            $this->list[self::COMPUTER_ROOM],
            $this->list[self::ASSEMBLY_HALL],
        ];
    }
}