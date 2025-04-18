<?php

namespace common\components\dictionaries\base;

class PersonalDataDictionary extends BaseDictionary
{
    const SURNAME = 1;
    const FIRSTNAME = 2;
    const PATRONYMIC = 3;
    const BIRTH_YEAR = 4;
    const BIRTH_MONTH = 5;
    const BIRTH_DAY = 6;
    const EDUCATIONAL_INSTITUTION = 7;
    const DIGITAL_FACE_IMAGE = 8;
    const VIDEO_MATERIALS = 9;
    const SOUND_MATERIALS = 10;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::SURNAME => 'Фамилия',
            self::FIRSTNAME => 'Имя',
            self::PATRONYMIC => 'Отчество (при наличии)',
            self::BIRTH_YEAR => 'Год рождения',
            self::BIRTH_MONTH => 'Месяц рождения',
            self::BIRTH_DAY => 'Дата рождения',
            self::EDUCATIONAL_INSTITUTION => 'Наименование учебного заведения и класса, в котором обучается несовершеннолетний',
            self::DIGITAL_FACE_IMAGE => 'Цифровое фотографическое изображение лица',
            self::VIDEO_MATERIALS => 'Видео материалы с субъектом',
            self::SOUND_MATERIALS => 'Звуковые материалы с субъектом',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::SURNAME],
            $this->list[self::FIRSTNAME],
            $this->list[self::PATRONYMIC],
            $this->list[self::BIRTH_YEAR],
            $this->list[self::BIRTH_MONTH],
            $this->list[self::BIRTH_DAY],
            $this->list[self::EDUCATIONAL_INSTITUTION],
            $this->list[self::DIGITAL_FACE_IMAGE],
            $this->list[self::VIDEO_MATERIALS],
            $this->list[self::SOUND_MATERIALS],
        ];
    }
}