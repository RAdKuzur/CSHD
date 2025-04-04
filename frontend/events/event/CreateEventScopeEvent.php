<?php

namespace frontend\events\event;

use common\events\EventInterface;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use common\repositories\event\EventRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use Yii;

class CreateEventScopeEvent implements EventInterface
{
    private $eventId;
    private $scopes;

    private EventRepository $repository;

    public function __construct(
        $eventId,
        $scopes = ''
    )
    {
        $this->eventId = $eventId;
        $this->scopes = $scopes;
        $this->repository = Yii::createObject(EventRepository ::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        if ($this->scopes == '') {
            return $this->repository->prepareResetScopes($this->eventId);
        }

        return
            array_merge(
                $this->repository->prepareResetScopes($this->eventId),
                $this->repository->prepareConnectScopes($this->eventId, $this->scopes)
            );
    }
}