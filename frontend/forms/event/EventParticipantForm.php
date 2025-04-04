<?php

namespace frontend\forms\event;

use frontend\models\work\team\ActParticipantWork;
use frontend\models\work\team\SquadParticipantWork;
use common\components\dictionaries\base\BranchDictionary;
use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class EventParticipantForm extends Model
{
    use EventTrait;

    private ActParticipantRepository $actParticipantRepository;
    private SquadParticipantRepository $squadParticipantRepository;

    public ActParticipantWork $actParticipant;

    public array $branches;
    public $form;
    public $fileMaterial;
    public $fileMaterialTable;

    public function __construct(
        $actParticipantId,
        ActParticipantRepository $actParticipantRepository = null,
        SquadParticipantRepository $squadParticipantRepository = null,
        $config = []
    )
    {
        parent::__construct($config);
        if (!$actParticipantRepository) {
            $actParticipantRepository = Yii::createObject(ActParticipantRepository::class);
        }
        if (!$squadParticipantRepository) {
            $squadParticipantRepository = Yii::createObject(SquadParticipantRepository::class);
        }
        $this->actParticipantRepository = $actParticipantRepository;
        $this->squadParticipantRepository = $squadParticipantRepository;

        $this->actParticipant = $this->actParticipantRepository->get($actParticipantId);
        $this->branches = ArrayHelper::getColumn(
            $this->actParticipantRepository->getParticipantBranches($actParticipantId), 'branch'
        );

        $this->fileMaterialTable = $this->fillTable();
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['form'], 'integer']
        ]);
    }

    public function fillTable()
    {
        $materials = $this->actParticipant->getFileLinks(FilesHelper::TYPE_MATERIAL);
        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($materials, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($materials), $this->actParticipant->foreign_event_id), 'fileId' => ArrayHelper::getColumn($materials, 'id')])
            ]
        );
    }

    public function getParticipantSurname()
    {
        /** @var SquadParticipantWork[] $squad */
        $squad = $this->squadParticipantRepository->getAllByActId($this->actParticipant->id);
        return $squad[0]->participantWork->surname;
    }

    public function save()
    {
        $this->actParticipant->form = $this->form;
        $this->actParticipantRepository->save($this->actParticipant);
    }

    public function getEventStartDate()
    {
        return $this->actParticipant->foreignEventWork->begin_date;
    }

    public function getForeignEventName()
    {
        return $this->actParticipant->foreignEventWork->name;
    }
}