<?php

namespace frontend\models\work\general;

use common\events\EventTrait;
use common\models\scaffold\Files;

class FilesWork extends Files
{
    use EventTrait;

    public static function fill(
        $tableName,
        $tableRowId,
        $filetype,
        $filepath
    )
    {
        $entity = new static();
        $entity->table_name = $tableName;
        $entity->table_row_id = $tableRowId;
        $entity->file_type = $filetype;
        $entity->filepath = $filepath;

        return $entity;
    }
}