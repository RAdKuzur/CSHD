<?php

namespace frontend\forms\training_group;

use common\Model;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\general\PeoplePositionCompanyBranchRepository;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use Yii;

class ProtocolForm extends Model
{
    /** @var TrainingGroupParticipantWork[] $possibleParticipants */
    /** @var PeoplePositionCompanyBranchWork[] $ResponsiblePeople */
    public array $ResponsiblePeople;
    public array $possibleParticipants;
    public TrainingGroupWork $group;

    public $name;
    public $participants;
    public $teachers;
    public $responsiblepeople;
    public $bosses;

    public function __construct(
        TrainingGroupWork $group,
        $config = []
    )
    {
        parent::__construct($config);
        $this->group = $group;
        $this->possibleParticipants = (Yii::createObject(TrainingGroupParticipantRepository::class))->getSuccessParticipantsFromGroup($this->group->id);
        $this->ResponsiblePeople = (Yii::createObject(PeoplePositionCompanyBranchRepository::class))->getResponsiblePeopleByBranch($this->group->branch);
        // Собираем все people_id учителей
        $teacherPeopleIds = [];
       foreach ($this->group->teachersWork as $teacher) {
           $teacherPeopleIds[] = $teacher->getPeopleId();
        }

        // Фильтруем ResponsiblePeople, удаляя ID учителей
        $this->ResponsiblePeople = array_filter($this->ResponsiblePeople, function($peopleId) use ($teacherPeopleIds) {
            return !in_array($peopleId, $teacherPeopleIds);
        });
        // Переиндексировать массив (убрать пропуски в ключах после array_filter)
        $this->ResponsiblePeople = array_values($this->ResponsiblePeople);
    }

    public function rules()
    {
        return [
            [['name'], 'string'],
            [['participants', 'teachers'], 'safe'],
        ];
    }

    public function getNumberGroup()
    {
        return $this->group->number;
    }
}