<?php

namespace common\components\dictionaries\base;

class CertificateTypeDictionary extends BaseDictionary
{
    const PROJECT_PITCH = 1;
    const CONTROL_WORK = 2;
    const OTHER_CONTROL = 3;
    const OPEN_LESSON = 4;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::PROJECT_PITCH => 'Завершение с защитой проекта',
            self::CONTROL_WORK => 'Завершение с итоговой контрольной работой',
            self::OTHER_CONTROL => 'Иные формы контроля',
            self::OPEN_LESSON => 'Открытый урок',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::PROJECT_PITCH],
            $this->list[self::CONTROL_WORK],
            $this->list[self::OTHER_CONTROL],
            $this->list[self::OPEN_LESSON],
        ];
    }
}