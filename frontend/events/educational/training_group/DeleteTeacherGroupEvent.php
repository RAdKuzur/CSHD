<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\educational\TeacherGroupRepository;
use Yii;

class DeleteTeacherGroupEvent implements EventInterface
{
    private $id;

    private TeacherGroupRepository $repository;

    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->repository = Yii::createObject(TeacherGroupRepository::class);
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