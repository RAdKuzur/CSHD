<?php

namespace frontend\events\foreign_event;

use common\events\EventInterface;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use common\repositories\event\ParticipantAchievementRepository;
use frontend\models\work\event\ParticipantAchievementWork;
use Yii;

class ParticipantAchievementEvent implements EventInterface
{
    private ParticipantAchievementWork $model;

    private ParticipantAchievementRepository $repository;

    public function __construct(
        ParticipantAchievementWork $model
    )
    {
        $this->model = $model;
        $this->repository = Yii::createObject(ParticipantAchievementRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareCreate(
                $this->model->act_participant_id,
                $this->model->achievement,
                $this->model->type,
                $this->model->cert_number,
                $this->model->nomination,
                $this->model->date
            )
        ];
    }
}