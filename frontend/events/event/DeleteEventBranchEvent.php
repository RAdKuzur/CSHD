<?php

namespace frontend\events\event;

use common\events\EventInterface;
use common\repositories\event\EventRepository;
use Yii;

class DeleteEventBranchEvent implements EventInterface
{
    private $eventId;

    private EventRepository $repository;

    public function __construct(
        $eventId
    )
    {
        $this->eventId = $eventId;
        $this->repository = Yii::createObject(EventRepository ::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return
            $this->repository->prepareResetBranches($this->eventId);
    }
}