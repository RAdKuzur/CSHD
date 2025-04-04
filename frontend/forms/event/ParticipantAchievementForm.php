<?php

namespace frontend\forms\event;

use common\Model;
use common\repositories\event\ParticipantAchievementRepository;
use frontend\models\work\event\ParticipantAchievementWork;
use Yii;

class ParticipantAchievementForm extends Model
{
    public $id;
    public ?ParticipantAchievementWork $entity;

    private ParticipantAchievementRepository $repository;

    // Поля формы
    public $achievement;
    public $certNumber;
    public $date;
    public $type;

    public function __construct(
        $achievementId,
        ParticipantAchievementRepository $repository = null,
        $config = []
    )
    {
        parent::__construct($config);
        if (!$repository) {
            $repository = Yii::createObject(ParticipantAchievementRepository::class);
        }
        /** @var ParticipantAchievementRepository $repository */
        $this->repository = $repository;

        $this->id = $achievementId;
        $this->entity = $this->repository->get($achievementId);

        $this->achievement = $this->entity->achievement;
        $this->certNumber = $this->entity->cert_number;
        $this->date = $this->entity->date;
        $this->type = $this->entity->type;
    }

    public function rules()
    {
        return [
            [['achievement', 'certNumber'], 'string'],
            [['type'], 'integer'],
            [['date'], 'safe'],
        ];
    }

    public function save()
    {
        $this->entity->achievement = $this->achievement;
        $this->entity->cert_number = $this->certNumber;
        $this->entity->date = $this->date;
        $this->entity->type = $this->type;
        $this->repository->save($this->entity);
    }

}