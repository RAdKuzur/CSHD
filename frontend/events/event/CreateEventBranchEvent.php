<?php

namespace frontend\events\event;

use common\events\EventInterface;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use common\repositories\event\EventRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use Yii;

class CreateEventBranchEvent implements EventInterface
{
    private $eventId;
    private $branches;

    private EventRepository $repository;

    public function __construct(
        $eventId,
        $branches = ''
    )
    {
        $this->eventId = $eventId;
        $this->branches = $branches;
        $this->repository = Yii::createObject(EventRepository ::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        if ($this->branches == '') {
            return $this->repository->prepareResetBranches($this->eventId);
        }

        return
            array_merge(
                $this->repository->prepareResetBranches($this->eventId),
                $this->repository->prepareConnectBranches($this->eventId, $this->branches)
            );
    }
}