<?php

namespace common\components\dictionaries\base;

class ThematicDirectionDictionary extends BaseDictionary
{
    const IT_PROGRAMMING = 1;
    const IT_CRYPTO = 2;
    const MEDIA_JOURNALISM = 6;
    const MEDIA_PHOTO = 8;
    const DIGITAL_MANUFACTURE = 10;
    const BIOLOGY = 11;
    const IT_AR_VR = 12;
    const NANO = 15;
    const TECH_MODEL_START = 16;
    const DECORATIVE_APPLIED_ARTS = 19;
    const CLOTHING_DESIGN_MODELING = 20;
    const BIKE_MOTO_TRIAL = 21;
    const TECH_MODEL_GENERAL = 22;
    const RADIO_DIRECTION = 23;
    const PHYSICS = 27;
    const CHEMISTRY = 28;
    const IT_DIGITAL_GRAPHIC = 29;
    const IT_ELECTRONIC = 30;
    const ROBOT_MOBILE = 31;
    const ROBOT_UNDERWATER = 32;
    const RADIO_TECH = 33;
    const MEDIA_SOUND = 34;
    const FLY_MULTI_COPTER = 35;
    const FLY_AIRPLANE = 36;
    const WATER_SHIP = 37;
    const AUTO_MODEL = 38;
    const ARCHITECTURE = 39;
    const SCHOOL_PREPARE = 40;
    const COMPLEX_PROF_ORIENTATION = 41;
    const GENERAL_INTELLECTUAL = 42;
    const VOCAL = 43;
    const ARTIST = 44;
    const LITERATURE = 45;
    const LINGUISTICS = 46;
    const CHOREOGRAPHY = 47;
    const ARTISTIC_ART = 48;
    const YACHTING = 49;
    const MATH = 50;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::IT_PROGRAMMING => 'Информационные технологии: программирование',
            self::IT_CRYPTO => 'Информационные технологии: криптография',
            self::MEDIA_JOURNALISM => 'Медиатехнологии: журналистика',
            self::MEDIA_PHOTO => 'Медиатехнологии: фото и видео',
            self::DIGITAL_MANUFACTURE => 'Цифровое производство и прототипирование',
            self::BIOLOGY => 'Биология',
            self::IT_AR_VR => 'Информационные технологии: дополненная и виртуальная реальность',
            self::NANO => 'Нанотехнологии',
            self::TECH_MODEL_START => 'Начальное техническое моделирование',
            self::DECORATIVE_APPLIED_ARTS => 'Искусство декоративно-прикладное',
            self::CLOTHING_DESIGN_MODELING => 'Одежды дизайн и моделирование',
            self::BIKE_MOTO_TRIAL => 'Веломототриал',
            self::TECH_MODEL_GENERAL => 'Общее техническое моделирование',
            self::RADIO_DIRECTION => 'Радиопеленгация и спортивное ориентирование',
            self::PHYSICS => 'Физика',
            self::CHEMISTRY => 'Химия',
            self::IT_DIGITAL_GRAPHIC => 'Информационные технологии: цифровая графика',
            self::IT_ELECTRONIC => 'Информационные технологии: электроника',
            self::ROBOT_MOBILE => 'Робототехника мобильная',
            self::ROBOT_UNDERWATER => 'Робототехника подводная',
            self::RADIO_TECH => 'Радиотехническое конструирование',
            self::MEDIA_SOUND => 'Медиатехнологии: звуковой монтаж',
            self::FLY_MULTI_COPTER => 'Летательные аппараты: мультикоптеры',
            self::FLY_AIRPLANE => 'Летательные аппараты: самолеты',
            self::WATER_SHIP => 'Водный транспорт: судомоделирование',
            self::AUTO_MODEL => 'Автомобильный транспорт: моделирование',
            self::ARCHITECTURE => 'Архитектура и дизайн зданий',
            self::SCHOOL_PREPARE => 'Подготовка детей к школе',
            self::COMPLEX_PROF_ORIENTATION => 'Комплексная профориентация детей',
            self::GENERAL_INTELLECTUAL => 'Общее интеллектуальное развитие',
            self::VOCAL => 'Вокал (вольное искусство)',
            self::ARTIST => 'Актерское мастерство',
            self::LITERATURE => 'Литературное творчество',
            self::LINGUISTICS => 'Лингвистика',
            self::CHOREOGRAPHY => 'Хореография',
            self::ARTISTIC_ART => 'Художественно-эстетическое искусство',
            self::YACHTING => 'Парусный спорт (яхтинг)',
            self::MATH => 'Математика',
        ];
    }

    public function getAbbreviations()
    {
        return [
            self::IT_PROGRAMMING => 'ИТП',
            self::IT_CRYPTO => 'ИТК',
            self::MEDIA_JOURNALISM => 'МЖУ',
            self::MEDIA_PHOTO => 'МФВ',
            self::DIGITAL_MANUFACTURE => 'ЦПП',
            self::BIOLOGY => 'БИО',
            self::IT_AR_VR => 'ДВР',
            self::NANO => 'НАН',
            self::TECH_MODEL_START => 'НТМ',
            self::DECORATIVE_APPLIED_ARTS => 'ИДП',
            self::CLOTHING_DESIGN_MODELING => 'ОДМ',
            self::BIKE_MOTO_TRIAL => 'ВМТ',
            self::TECH_MODEL_GENERAL => 'ОТМ',
            self::RADIO_DIRECTION => 'РСО',
            self::PHYSICS => 'ФИЗ',
            self::CHEMISTRY => 'ХИМ',
            self::IT_DIGITAL_GRAPHIC => 'ИТГ',
            self::IT_ELECTRONIC => 'ИТЭ',
            self::ROBOT_MOBILE => 'РОМ',
            self::ROBOT_UNDERWATER => 'РОП',
            self::RADIO_TECH => 'РТК',
            self::MEDIA_SOUND => 'МЗВ',
            self::FLY_MULTI_COPTER => 'ЛАК',
            self::FLY_AIRPLANE => 'ЛАС',
            self::WATER_SHIP => 'СУМ',
            self::AUTO_MODEL => 'АТМ',
            self::ARCHITECTURE => 'АРД',
            self::SCHOOL_PREPARE => 'ПДШ',
            self::COMPLEX_PROF_ORIENTATION => 'КПД',
            self::GENERAL_INTELLECTUAL => 'ОИР',
            self::VOCAL => 'ВКЛ',
            self::ARTIST => 'АКТ',
            self::LITERATURE => 'ЛИТ',
            self::LINGUISTICS => 'ЛНГ',
            self::CHOREOGRAPHY => 'ХРГ',
            self::ARTISTIC_ART => 'ХЭИ',
            self::YACHTING => 'ЯХТ',
            self::MATH => 'МАТ',
        ];
    }

    public function getAbbreviation($index)
    {
        return $this->getAbbreviations()[$index];
    }

    public function getFullnameList()
    {
        $names = $this->getList();
        $abbr = $this->getAbbreviations();
        $result = [];

        foreach ($names as $i => $value) {
            $result[$i] = "$value ($abbr[$i])";
        }

        return $result;
    }

    public function customSort()
    {
        return $this->list;
    }
}