<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\educational\TeacherGroupRepository;
use Yii;

class DeleteTeachersFromGroupEvent implements EventInterface
{
    private $groupId;

    private TeacherGroupRepository $repository;

    public function __construct(
        $groupId
    )
    {
        $this->groupId = $groupId;
        $this->repository = Yii::createObject(TeacherGroupRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        $result = [];
        $teachers = $this->repository->getAllTeachersFromGroup($this->groupId);
        foreach ($teachers as $teacher) {
            $result[] = $this->repository->prepareDelete($teacher->id);
        }

        return $result;
    }
}