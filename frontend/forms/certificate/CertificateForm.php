<?php

namespace frontend\forms\certificate;

use common\Model;
use common\repositories\educational\CertificateRepository;
use common\repositories\educational\CertificateTemplatesRepository;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\CertificateTemplatesWork;
use frontend\models\work\educational\CertificateWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\db\ActiveQuery;

class CertificateForm extends Model
{
    public $id;
    /**
     * @var CertificateTemplatesWork[] $templates
     * @var TrainingGroupWork[] $groups
     */
    public array $templates;
    public array $groups;

    public ActiveQuery $groupQuery;
    public ActiveQuery $participantQuery;

    public CertificateWork $entity;

    public int $templateId;
    public ?array $participants;

    public function __construct(
        ActiveQuery $groupQuery = null,
        ActiveQuery $participantQuery = null,
        int $id = -1,
        $config = [])
    {
        parent::__construct($config);
        $this->templates = (Yii::createObject(CertificateTemplatesRepository::class))->getAll();
        $this->groups = (Yii::createObject(TrainingGroupRepository::class))->getGroupsForCertificates();

        $this->id = $id;
        if ($id != -1) {
            $this->entity = (Yii::createObject(CertificateRepository::class))->get($id);
        }

        $this->templateId = $this->templates[0]->id;

        if (!$groupQuery) {
            $this->groupQuery = TrainingGroupWork::find();
        }
        else {
            $this->groupQuery = $groupQuery;
        }

        if (!$participantQuery) {
            $this->participantQuery = TrainingGroupParticipantWork::find()->where('0=1');
        }
        else {
            $this->participantQuery = $participantQuery;
        }
    }

    public function load($data, $formName = null)
    {
        $this->participants = $data['group-participant-selection'];
        return parent::load($data, $formName);
    }
}