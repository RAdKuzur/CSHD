<?php

namespace frontend\forms\training_group;

use common\Model;
use common\repositories\educational\TrainingGroupParticipantRepository;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;

class ProtocolForm extends Model
{
    /** @var TrainingGroupParticipantWork[] $possibleParticipants */
    public array $possibleParticipants;
    public TrainingGroupWork $group;

    public $name;
    public $participants;

    public function __construct(
        TrainingGroupWork $group,
        $config = []
    )
    {
        parent::__construct($config);
        $this->group = $group;
        $this->possibleParticipants = (Yii::createObject(TrainingGroupParticipantRepository::class))->getSuccessParticipantsFromGroup($this->group->id);
    }

    public function rules()
    {
        return [
            [['name'], 'string'],
            [['participants'], 'safe']
        ];
    }
}