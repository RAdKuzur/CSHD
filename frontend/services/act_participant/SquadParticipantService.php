<?php

namespace frontend\services\act_participant;

use frontend\events\act_participant\SquadParticipantCreateEvent;
use frontend\events\act_participant\SquadParticipantDeleteEvent;
use frontend\models\work\team\ActParticipantWork;
use frontend\models\work\team\SquadParticipantWork;
use frontend\models\work\team\TeamNameWork;
use common\Model;
use common\models\scaffold\SquadParticipant;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use common\repositories\team\TeamRepository;
use frontend\forms\OrderEventForm;
use yii\helpers\ArrayHelper;

class SquadParticipantService
{
    public ActParticipantRepository $actParticipantRepository;
    public TeamRepository $teamRepository;
    public SquadParticipantRepository $squadParticipantRepository;
    public function __construct(
        ActParticipantRepository $actParticipantRepository,
        TeamRepository $teamRepository,
        SquadParticipantRepository $squadParticipantRepository
    )
    {
        $this->actParticipantRepository = $actParticipantRepository;
        $this->teamRepository = $teamRepository;
        $this->squadParticipantRepository = $squadParticipantRepository;
    }
    public function addSquadParticipantEvent(ActParticipantWork $model, $participants, $actId){
        if($participants != NULL) {
            foreach ($participants as $participant) {
                if($this->squadParticipantRepository->getCountByActAndParticipantId($actId, $participant) == 0) {
                    $model->recordEvent(new SquadParticipantCreateEvent($actId, $participant), SquadParticipantWork::class);
                }
            }
            $model->releaseEvents();
        }
    }
    public function updateSquadParticipantEvent(ActParticipantWork $model, $participants){
        $existParticipant = ArrayHelper::getColumn($this->squadParticipantRepository->getAllByActId($model->id), 'participant_id');
        if($existParticipant != NULL && $participants != NULL) {
            $deleteSquadParticipant = array_diff($existParticipant, $participants);
            $addSquadParticipant = array_diff($participants, $existParticipant);
        }
        else if($participants == NULL) {
            $deleteSquadParticipant = $existParticipant;

        }
        else if($existParticipant == NULL) {
            $addSquadParticipant = $participants;
        }
        else {
            $deleteSquadParticipant = NULL;
            $addSquadParticipant = NULL;
        }
        if($deleteSquadParticipant != NULL) {
            foreach ($deleteSquadParticipant as $participant) {
                if($this->squadParticipantRepository->getCountByActAndParticipantId($model->id, $participant) != 0) {
                    $model->recordEvent(new SquadParticipantDeleteEvent($model->id, $participant),SquadParticipantWork::class);
                }
            }
        }
        if($addSquadParticipant != NULL) {
            foreach ($addSquadParticipant as $participant) {
                if($this->squadParticipantRepository->getCountByActAndParticipantId($model->id, $participant) == 0) {
                    $model->recordEvent(new SquadParticipantCreateEvent($model->id, $participant), SquadParticipantWork::class);
                }
            }
        }
        $model->releaseEvents();
    }
}