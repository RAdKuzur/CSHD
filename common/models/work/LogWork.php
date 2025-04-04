<?php

namespace common\models\work;

use common\models\scaffold\Log;

class LogWork extends Log
{
    public static function fill(
        string $datetime,
        int $level,
        int $type,
        ?int $userId,
        string $text,
        string $addData = ''
    )
    {
        $entity = new static();
        $entity->datetime = $datetime;
        $entity->level = $level;
        $entity->type = $type;
        $entity->user_id = $userId;
        $entity->text = $text;
        $entity->add_data = $addData;

        return $entity;
    }

    public function setAddData(string $addData)
    {
        $this->add_data = $addData;
    }
}