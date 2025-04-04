<?php

namespace frontend\models\work\educational\training_group;

use common\models\scaffold\TrainingGroupExpert;
use common\models\scaffold\TrainingGroupLesson;
use common\repositories\dictionaries\AuditoriumRepository;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\general\PeopleStampWork;
use Yii;

/**
 * @property PeopleStampWork $expertWork
 */
class TrainingGroupExpertWork extends TrainingGroupExpert
{
    const TYPE_EXTERNAL = 1;
    const TYPE_INTERNAL = 2;

    public function getExpertTypeString()
    {
        return $this->expert_type == self::TYPE_EXTERNAL ? "Внешний" : "Внутренний";
    }

    public static function fill(int $groupId, int $expertId, int $expertType, int $id = null)
    {
        $entity = new static();
        $entity->id = $id;
        $entity->training_group_id = $groupId;
        $entity->expert_id = $expertId;
        $entity->expert_type = $expertType;

        return $entity;
    }

    public function __toString()
    {
        return "[GroupID: $this->training_group_id]
                [ExpertID: $this->expert_id]
                [ExpertType: $this->expert_type]]";
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['id', 'integer'],
            [['expert_id', 'expert_type'], 'required'],
            ['expertId', 'safe']
        ]);
    }

    public function getExpertId()
    {
        return $this->expertWork->people_id;
    }

    public function setExpertId($expertId)
    {
        return $this->expertId = $expertId;
    }

    public function getExpertWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'expert_id']);
    }
}