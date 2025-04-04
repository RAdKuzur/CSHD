<?php

namespace frontend\forms;

use frontend\models\work\order\OrderMainWork;
use common\helpers\files\FilesHelper;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\regulation\RegulationWork;
use frontend\models\work\responsibility\LegacyResponsibleWork;
use frontend\models\work\responsibility\LocalResponsibilityWork;
use yii\base\Model;
use yii\web\UploadedFile;

class ResponsibilityForm extends Model
{
    public $responsibilityType;
    public $branch;
    public $auditoriumId;
    public $quant;
    public $peopleStampId;
    public $startDate;
    public $endDate;
    public $orderId;
    public $regulationId;
    public $filesList;

    public $filesStr;

    public function rules()
    {
        return [
            [['orderId', 'peopleStampId'] , 'required'],
            [['responsibilityType', 'branch', 'auditoriumId', 'quant', 'peopleStampId', 'regulationId', 'orderId'], 'integer'],
            [['auditoriumId'], 'exist', 'skipOnError' => true, 'targetClass' => AuditoriumWork::class, 'targetAttribute' => ['auditoriumId' => 'id']],
            [['peopleStampId'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStampWork::class, 'targetAttribute' => ['peopleStampId' => 'id']],
            [['regulationId'], 'exist', 'skipOnError' => true, 'targetClass' => RegulationWork::class, 'targetAttribute' => ['regulationId' => 'id']],
            [['orderId'], 'exist', 'skipOnError' => true, 'targetClass' => OrderMainWork::class, 'targetAttribute' => ['orderId' => 'id']],
            [['startDate', 'endDate', 'filesStr'], 'safe'],
            [['filesList'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10]
        ];
    }
    public function attributeLabels(){
        return [
            'responsibilityType' => 'Вид ответственности',
            'branch' => 'Отдел',
            'auditoriumId' => 'Помещение',
            'quant' => 'Квант',
            'peopleStampId' => 'Работник',
            'orderId' => 'Приказ',
            'regulationId' => 'Положение/инструкция'
        ];
    }

    public static function fillFromModels(LocalResponsibilityWork $responsibility, LegacyResponsibleWork $legacy = null)
    {
        $entity = new static();
        $entity->responsibilityType = $responsibility->responsibility_type;
        $entity->branch = $responsibility->branch;
        $entity->auditoriumId = $responsibility->auditorium_id;
        $entity->quant = $responsibility->quant;
        $entity->peopleStampId = $responsibility->people_stamp_id;
        $entity->regulationId = $responsibility->regulation_id;
        $entity->filesStr = $responsibility->getFileLinks(FilesHelper::TYPE_OTHER);

        if ($legacy !== null) {
            $entity->startDate = $legacy->start_date;
            $entity->endDate = $legacy->end_date;
            $entity->orderId = $legacy->order_id;
        }

        return $entity;
    }

    // Проверка на тип отправленной формы (прикрепление)
    public function isAttach()
    {
        return $this->peopleStampId !== null;
    }

    // Проверка на тип отправленной формы (открепление)
    public function isDetach()
    {
        return $this->endDate !== null;
    }

    public function getFilesInstances(ResponsibilityForm $model)
    {
        $model->filesList = UploadedFile::getInstances($model, 'filesList');
    }
}