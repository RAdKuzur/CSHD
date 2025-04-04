<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\educational\TeacherGroupRepository;
use Yii;

class AddTeachersToGroupEvent implements EventInterface
{
    private $groupId;
    private $peopleIds;

    private TeacherGroupRepository $repository;

    public function __construct(
        $groupId,
        $peopleIds
    )
    {
        $this->groupId = $groupId;
        $this->peopleIds = $peopleIds;
        $this->repository = Yii::createObject(TeacherGroupRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        $result = [];
        foreach ($this->peopleIds as $peopleId) {
            $result[] = $this->repository->prepareCreate($peopleId, $this->groupId);
        }

        return $result;
    }
}