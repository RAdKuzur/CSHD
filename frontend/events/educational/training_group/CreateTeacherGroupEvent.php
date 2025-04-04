<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\educational\TeacherGroupRepository;
use Yii;

class CreateTeacherGroupEvent implements EventInterface
{
    private $groupId;
    private $teacherId;

    private TeacherGroupRepository $repository;

    public function __construct(
        $groupId,
        $teacherId
    )
    {
        $this->groupId = $groupId;
        $this->teacherId = $teacherId;
        $this->repository = Yii::createObject(TeacherGroupRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareCreate($this->teacherId, $this->groupId)
        ];
    }
}