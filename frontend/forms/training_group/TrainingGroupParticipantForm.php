<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\helpers\html\HtmlBuilder;
use common\helpers\html\HtmlCreator;
use common\helpers\StringFormatter;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class TrainingGroupParticipantForm extends Model
{
    use EventTrait;

    public $id;
    public $number;
    public $participants;
    public $prevParticipants;

    public TrainingGroupWork $group;
    public $participantFile;
    public $participantsTable;

    public function __construct($id = -1, $config = [])
    {
        parent::__construct($config);
        if ($id !== -1) {
            $this->participants = (Yii::createObject(TrainingGroupRepository::class))->getParticipants($id);
            $this->prevParticipants = (Yii::createObject(TrainingGroupRepository::class))->getParticipants($id);
            $this->number = (Yii::createObject(TrainingGroupRepository::class))->get($id)->number;
            $this->id = $id;
            $this->group = (Yii::createObject(TrainingGroupRepository::class))->get($id);
            $this->participantsTable = $this->createParticipantTable();
        }
        else {
            $this->prevParticipants = [];
        }
    }

    public function rules()
    {
        return [
            [['participants'], 'safe'],
            [['participantFile'], 'file', 'extensions' => 'xls, xlsx', 'skipOnEmpty' => true],
        ];
    }

    private function createParticipantTable()
    {
        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['ФИО обучающегося'], array_map(function (TrainingGroupParticipantWork $groupParticipantWork) {
                    return StringFormatter::stringAsLink(
                        $groupParticipantWork->participantWork->getFullFio(),
                        Url::to([Yii::$app->frontUrls::PARTICIPANT_VIEW, 'id' => $groupParticipantWork->participant_id])
                    );
                }, $this->participants)),
                array_merge(['Способ доставки сертификата'], array_map(function (TrainingGroupParticipantWork $groupParticipantWork) {
                    return Yii::$app->sendMethods->get($groupParticipantWork->send_method);
                }, $this->participants)),
                ['']
            ],
            [
                HtmlBuilder::createButtonsArray(
                    HtmlCreator::IconDelete(),
                    Url::to('delete-participant'),
                    [
                        'groupId' => array_fill(0, count($this->participants), $this->id),
                        'entityId' => ArrayHelper::getColumn($this->participants, 'id')
                    ]
                )
            ]
        );
    }
}