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

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::PATRIOTIC_EDUCATION => 'Патриотическое воспитание ',
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