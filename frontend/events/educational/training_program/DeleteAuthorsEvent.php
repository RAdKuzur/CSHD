<?php

namespace frontend\events\educational\training_program;

use common\events\EventInterface;
use common\repositories\educational\TrainingProgramRepository;
use frontend\models\work\educational\training_program\AuthorProgramWork;
use Yii;

class DeleteAuthorsEvent implements EventInterface
{
    private $programId;
    private TrainingProgramRepository $repository;

    public function __construct($programId)
    {
        $this->programId = $programId;
        $this->repository = Yii::createObject(TrainingProgramRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        $authors = $this->repository->getAuthors($this->programId);
        $result = [];
        foreach ($authors as $author) {
            /** @var AuthorProgramWork $author */
            $result[] = $this->repository->prepareDeleteAuthor($author->id);
        }

        return $result;
    }
}