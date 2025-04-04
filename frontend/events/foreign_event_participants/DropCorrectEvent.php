<?php

namespace frontend\events\foreign_event_participants;

use common\events\EventInterface;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use Yii;

class DropCorrectEvent implements EventInterface
{
    private $participantId;
    private $dropType;

    private ForeignEventParticipantsRepository $repository;

    public function __construct(
        $participantId,
        $dropType = ForeignEventParticipantsWork::DROP_CORRECT_HARD
    )
    {
        $this->participantId = $participantId;
        $this->dropType = $dropType;
        $this->repository = Yii::createObject(ForeignEventParticipantsRepository ::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        /** @var ForeignEventParticipantsWork $model */
        $model = $this->repository->get($this->participantId);
        $model->setNotTrue($this->dropType);

        return [
            $this->repository->prepareUpdate($model)
        ];
    }
}