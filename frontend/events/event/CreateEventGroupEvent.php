<?php

namespace frontend\events\event;

use common\events\EventInterface;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use common\repositories\event\EventGroupRepository;
use common\repositories\event\EventRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\event\EventGroupWork;
use Yii;

class CreateEventGroupEvent implements EventInterface
{
    private $groupId;
    private $eventId;

    private EventGroupRepository $repository;

    public function __construct(
        $groupId,
        $eventId
    )
    {
        $this->groupId = $groupId;
        $this->eventId = $eventId;
        $this->repository = Yii::createObject(EventGroupRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {

        return[
            $this->repository->prepareCreate(
                $this->groupId,
                $this->eventId
            )
        ];
    }
}