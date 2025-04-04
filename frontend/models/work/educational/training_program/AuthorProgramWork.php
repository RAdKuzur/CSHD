<?php

namespace frontend\models\work\educational\training_program;

use common\models\scaffold\AuthorProgram;
use frontend\models\work\general\PeopleStampWork;

/** @property PeopleStampWork $authorWork */

class AuthorProgramWork extends AuthorProgram
{
    public static function fill($programId, $authorId)
    {
        $entity = new static();
        $entity->training_program_id = $programId;
        $entity->author_id = $authorId;

        return $entity;
    }

    public function getAuthorWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'author_id']);
    }
}