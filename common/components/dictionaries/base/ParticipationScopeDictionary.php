<?php

namespace common\components\dictionaries\base;

class ParticipationScopeDictionary extends BaseDictionary
{
    const PATRIOTIC_EDUCATION = 1;
    const PREVENTION_BULLYING = 2;
    const ANTITERRORIST_MEASURES = 3;
    const ANTIDRUG_MEASURES = 4;
    const PREVENTION_SUICIDE = 5;
    const POSITIVE_THINKING = 6;
    const HEALTHY_LIFESTYLE = 7;
    const CHILD_ROAD_ACCIDENT = 8;
    const ECOLOGICAL_EDUCATION = 9;
    const RDDM = 10;
    const IT_PROGRAMMING = 11;
    const IT_CRYPTO = 12;
    const MEDIA_JOURNALISM = 13;
    const MEDIA_PHOTO_VIDEO = 14;
    const DIGITAL_MANUFACTURING = 15;
    const BIOLOGY = 16;
    const IT_AR_VR = 17;
    const NANOTECHNOLOGY = 18;
    const START_TECHNICAL_MODELING = 19;
    const DECORATIVE_APPLIED_ARTS = 20;
    const CLOTHING_DESIGN_MODELING = 21;
    const BIKE_MOTO_TRIAL = 22;
    const GENERAL_TECHNICAL_MODELING = 23;
    const RADIO_DIRECTION = 24;
    const PHYSICS = 25;
    const CHEMISTRY = 26;
    const IT_GRAPH = 27;
    const IT_ELECTRIC = 28;
    const ROBO_MOBILE = 29;
    const ROBO_UNDERWATER = 30;
    const RADIO_CONSTRUCT = 31;
    const MEDIA_SOUND = 32;
    const FLY_COPTER = 33;
    const FLY_PLANE = 34;
    const WATER_SHIP = 35;
    const AUTO_MODEL = 36;
    const ARCHITECTURE = 37;
    const PRE_SCHOOL = 38;
    const COMMON_INTEL = 40;
    const VOCAL = 41;
    const ACTOR_ART = 42;
    const LITERATURE = 43;
    const LINGUISTICS = 44;
    const CHOREOGRAPHY = 45;
    const ARTISTIC_AESTHETIC = 46;
    const YACHTING = 47;
    const MATH = 48;
    const PROFESSIONAL = 49;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::PATRIOTIC_EDUCATION => 'Патриотическое воспитание',
            self::PREVENTION_BULLYING => 'Профилактика травли (буллинга)',
            self::ANTITERRORIST_MEASURES => 'Антитеррористические мероприятия',
            self::ANTIDRUG_MEASURES => 'Профилактические антинаркотические мероприятия',
            self::PREVENTION_SUICIDE => 'Профилактика суицидального поведения',
            self::POSITIVE_THINKING => 'Формирование позитивного мышления',
            self::HEALTHY_LIFESTYLE => 'Формирование принципов здорового образа жизни',
            self::CHILD_ROAD_ACCIDENT => 'Профилактика детского дорожно-транспортного травматизма',
            self::ECOLOGICAL_EDUCATION => 'Экологическое воспитание',
            self::RDDM => 'В рамках деятельности Российского движения детей и молодёжи (РДДМ)',
            self::IT_PROGRAMMING => 'Информационные технологии: программирование',
            self::IT_CRYPTO => 'Информационные технологии: криптография',
            self::MEDIA_JOURNALISM => 'Медиатехнологии: журналистика ',
            self::MEDIA_PHOTO_VIDEO => 'Медиатехнологии: фото и видео',
            self::DIGITAL_MANUFACTURING => 'Цифровое производство и прототипирование',
            self::BIOLOGY => 'Биология',
            self::IT_AR_VR => 'Информационные технологии: дополненная и виртуальная реальность',
            self::NANOTECHNOLOGY => 'Нанотехнологии',
            self::START_TECHNICAL_MODELING => 'Начальное техническое моделирование',
            self::DECORATIVE_APPLIED_ARTS => 'Искусство декоративно-прикладное',
            self::CLOTHING_DESIGN_MODELING => 'Одежды дизайн и моделирование',
            self::BIKE_MOTO_TRIAL => 'Веломототриал',
            self::GENERAL_TECHNICAL_MODELING => 'Общее техническое моделирование',
            self::RADIO_DIRECTION => 'Радиопеленгация и спортивное ориентирование',
            self::PHYSICS => 'Физика',
            self::CHEMISTRY => 'Химия',
            self::IT_GRAPH => 'Информационные технологии: цифровая графика',
            self::IT_ELECTRIC => 'Информационные технологии: электроника',
            self::ROBO_MOBILE => 'Робототехника мобильная',
            self::ROBO_UNDERWATER => 'Робототехника подводная',
            self::RADIO_CONSTRUCT => 'Радиотехническое конструирование',
            self::MEDIA_SOUND => 'Медиатехнологии: звуковой монтаж',
            self::FLY_COPTER => 'Летательные аппараты: мультикоптеры',
            self::FLY_PLANE => 'Летательные аппараты: самолеты',
            self::WATER_SHIP => 'Водный транспорт: судомоделирование',
            self::AUTO_MODEL => 'Автомобильный транспорт: моделирование',
            self::ARCHITECTURE => 'Архитектура и дизайн зданий',
            self::PRE_SCHOOL => 'Подготовка детей к школе',
            self::COMMON_INTEL => 'Общее интеллектуальное развитие',
            self::VOCAL => 'Вокал (вольное искусство)',
            self::ACTOR_ART => 'Актерское мастерство',
            self::LITERATURE => 'Литературное творчество',
            self::LINGUISTICS => 'Лингвистика',
            self::CHOREOGRAPHY => 'Хореография',
            self::ARTISTIC_AESTHETIC => 'Художественно-эстетическое искусство',
            self::YACHTING => 'Парусный спорт (яхтинг)',
            self::MATH => 'Математика',
            self::PROFESSIONAL => 'Профориентационные мероприятия',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::PATRIOTIC_EDUCATION],
            $this->list[self::PREVENTION_BULLYING],
            $this->list[self::ANTITERRORIST_MEASURES],
            $this->list[self::ANTIDRUG_MEASURES],
            $this->list[self::PREVENTION_SUICIDE],
            $this->list[self::POSITIVE_THINKING],
            $this->list[self::HEALTHY_LIFESTYLE],
            $this->list[self::CHILD_ROAD_ACCIDENT],
            $this->list[self::ECOLOGICAL_EDUCATION],
            $this->list[self::RDDM],
            $this->list[self::IT_PROGRAMMING],
            $this->list[self::IT_CRYPTO],
            $this->list[self::MEDIA_JOURNALISM],
            $this->list[self::MEDIA_PHOTO_VIDEO],
            $this->list[self::DIGITAL_MANUFACTURING],
            $this->list[self::BIOLOGY],
            $this->list[self::IT_AR_VR],
            $this->list[self::NANOTECHNOLOGY],
            $this->list[self::START_TECHNICAL_MODELING],
            $this->list[self::DECORATIVE_APPLIED_ARTS],
            $this->list[self::CLOTHING_DESIGN_MODELING],
            $this->list[self::BIKE_MOTO_TRIAL],
            $this->list[self::GENERAL_TECHNICAL_MODELING],
            $this->list[self::RADIO_DIRECTION],
            $this->list[self::PHYSICS],
        ];
    }
}