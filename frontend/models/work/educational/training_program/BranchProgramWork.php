<?php

namespace frontend\models\work\educational\training_program;

use common\models\scaffold\BranchProgram;

class BranchProgramWork extends BranchProgram
{
    public static function fill($programId, $branch)
    {
        $entity = new static();
        $entity->training_program_id = $programId;
        $entity->branch = $branch;

        return $entity;
    }
}