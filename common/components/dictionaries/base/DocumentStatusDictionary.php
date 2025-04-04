<?php

namespace common\components\dictionaries\base;

class DocumentStatusDictionary extends BaseDictionary
{
    const CURRENT = 1;
    const ARCHIVE = 2;
    const EXPIRED = 3;
    const NEEDANSWER = 4;
    const RESERVED = 5;
    const ANSWER = 6;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::CURRENT => 'Актуальные',
            self::ARCHIVE => 'Архивные',
            self::EXPIRED => 'Просроченные',
            self::NEEDANSWER => 'Требуют ответа',
            self::RESERVED => 'Резерные',
            self::ANSWER => 'Являются ответом',
        ];
    }

    public function getListDocIn()
    {
        return [
            self::CURRENT => 'Актуальные',
            self::ARCHIVE => 'Архивные',
            self::EXPIRED => 'Просроченные',
            self::NEEDANSWER => 'Требуют ответа',
            self::RESERVED => 'Резерные',
        ];
    }

    public function getListDocOut()
    {
        return [
            self::CURRENT => 'Актуальные',
            self::ARCHIVE => 'Архивные',
            self::RESERVED => 'Резерные',
            self::ANSWER => 'Являются ответом',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::CURRENT],
            $this->list[self::ARCHIVE],
            $this->list[self::EXPIRED],
            $this->list[self::NEEDANSWER],
            $this->list[self::RESERVED],
        ];
    }
}