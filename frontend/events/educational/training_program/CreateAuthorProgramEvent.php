<?php

namespace frontend\events\educational\training_program;

use common\events\EventInterface;
use common\repositories\educational\TrainingProgramRepository;
use Yii;

class CreateAuthorProgramEvent implements EventInterface
{
    private $programId;
    private $authorId;

    private TrainingProgramRepository $repository;

    public function __construct(
        $programId,
        $authorId
    )
    {
        $this->programId = $programId;
        $this->authorId = $authorId;
        $this->repository = Yii::createObject(TrainingProgramRepository ::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareCreateAuthorProgram($this->programId, $this->authorId)
        ];
    }
}