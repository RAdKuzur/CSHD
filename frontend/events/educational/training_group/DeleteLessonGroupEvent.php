<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use Yii;

class DeleteLessonGroupEvent implements EventInterface
{
    private $id;
    private TrainingGroupLessonRepository $repository;

    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->repository = Yii::createObject(TrainingGroupLessonRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareDelete($this->id)
        ];
    }
}