<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use Yii;

class DeleteVisitEvent implements EventInterface
{
    private $groupParticipantId;

    private VisitRepository $repository;

    public function __construct(
        $groupParticipantId
    )
    {
        $this->groupParticipantId = $groupParticipantId;
        $this->repository = Yii::createObject(VisitRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareDelete($this->groupParticipantId)
        ];
    }
}