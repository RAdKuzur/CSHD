<?php

namespace common\components\dictionaries\base;

class EventFormDictionary extends BaseDictionary
{
    const CONFERENCE = 1;
    const EXCURSION = 2;
    const LECTURE = 3;
    const COMPETITION = 4;
    const INTERACTIVE_GAME = 5;
    const FILM_SCREENING = 6;
    const CONTEST = 7;
    const MASTER_CLASS = 8;
    const EDUCATIONAL_SESSION = 9;
    const HOLIDAY = 10;
    const PROMOTION = 12;
    const INVITED_EXPERT = 13;
    const CLUB_MEETING = 14;
    const OPEN_DAY = 15;
    const INTELLECTUAL_GAME = 16;
    const WEBINAR = 17;
    const ORIENTATION_SEMINAR = 18;
    const OLYMPICS = 19;
    const ORIENTATION_SESSION = 21;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::CONFERENCE => 'Конференция',
            self::EXCURSION => 'Экскурсия',
            self::LECTURE => 'Лекция',
            self::COMPETITION => 'Соревнование',
            self::INTERACTIVE_GAME => 'Интерактивная игра',
            self::FILM_SCREENING => 'Кинопоказ',
            self::CONTEST => 'Конкурс',
            self::MASTER_CLASS => 'Мастер-класс',
            self::EDUCATIONAL_SESSION => 'Образовательная сессия',
            self::HOLIDAY => 'Праздник',
            self::PROMOTION => 'Акция',
            self::INVITED_EXPERT => 'Встреча с приглашенным экспертом',
            self::CLUB_MEETING => 'Заседание клуба',
            self::OPEN_DAY => 'День открытых дверей',
            self::INTELLECTUAL_GAME => 'Интеллектуальная игра',
            self::WEBINAR => 'Вебинар',
            self::ORIENTATION_SEMINAR => 'Установочный семинар',
            self::OLYMPICS => 'Олимпиада',
            self::ORIENTATION_SESSION => 'Установочное занятие',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::CONFERENCE],
            $this->list[self::EXCURSION],
            $this->list[self::LECTURE],
            $this->list[self::COMPETITION],
            $this->list[self::INTERACTIVE_GAME],
            $this->list[self::FILM_SCREENING],
            $this->list[self::CONTEST],
            $this->list[self::MASTER_CLASS],
            $this->list[self::EDUCATIONAL_SESSION],
            $this->list[self::HOLIDAY],
            $this->list[self::PROMOTION],
            $this->list[self::INVITED_EXPERT],
            $this->list[self::CLUB_MEETING],
            $this->list[self::OPEN_DAY],
            $this->list[self::INTELLECTUAL_GAME],
            $this->list[self::WEBINAR],
            $this->list[self::ORIENTATION_SEMINAR],
            $this->list[self::OLYMPICS],
            $this->list[self::ORIENTATION_SESSION],
        ];
    }
}