<?php

namespace common\components\dictionaries\base;

class AllowRemoteDictionary extends BaseDictionary
{
    const ONLY_PERSONAL = 1;
    const PERSONAL_WITH_REMOTE = 2;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::ONLY_PERSONAL => 'Только очная форма',
            self::PERSONAL_WITH_REMOTE => 'Очная форма, с применением дистанционных технологий',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::ONLY_PERSONAL],
            $this->list[self::PERSONAL_WITH_REMOTE],
        ];
    }
}