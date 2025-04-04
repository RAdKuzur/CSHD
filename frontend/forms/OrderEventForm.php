<?php

namespace frontend\forms;

use app\models\work\order\OrderEventGenerateWork;
use common\components\dictionaries\base\NomenclatureDictionary;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderEventWork;
use common\events\EventTrait;
use common\models\scaffold\People;
use common\models\scaffold\PeopleStamp;
use yii\base\Model;

class OrderEventForm extends Model {
    public $isNewRecord;
    use EventTrait;

    public $id;
    public $order_copy_id;
    public $order_number;
    public $order_postfix;
    public $order_date;
    public $order_name;
    public $signed_id;
    public $bring_id;
    public $executor_id;
    public $key_words;
    public $creator_id;
    public $last_edit_id;
    public $target;
    public $type;
    public $state;
    public $nomenclature_id;
    public $study_type;

    // карточка мероприятия
    public $eventName;
    public $organizer_id;
    public $dateBegin;
    public $dateEnd;
    public $city;
    public $minister;
    public $minAge;
    public $maxAge;
    public $eventWay;
    public $eventLevel;
    public $keyEventWords;
    //
    public $responsible_id;

    //Дополнительная информация для генерации приказа
    public $purpose;
    public $docEvent;
    public $documentDetails;
    public $respPeopleInfo;
    public $timeProvisionDay;
    public $extraRespInsert;
    public $timeInsertDay;
    public $extraRespMethod;
    public $extraRespInfoStuff;

    //награды и номинации
    public $team;
    public $award;
    public $teams;
    public $awards;
    public $participant_id;
    public $participant_personal_id;
    public $branch;
    public $teacher_id;
    public $teacher2_id;
    public $focus;
    public $formRealization;
    public $teamList;
    public $nominationList;
    //
    public $typeActParticipant;
    //
    public $scanFile;
    public $docFiles;
    public $actFiles;
    public function rules()
    {
        return [
            [['order_date', 'dateBegin', 'eventName', 'dateEnd','bring_id', 'executor_id', 'purpose', 'docEvent',
                'respPeopleInfo', 'timeProvisionDay', 'extraRespInsert', 'timeInsertDay', 'extraRespMethod', 'extraRespInfoStuff'], 'required'],
            [['order_copy_id', 'order_postfix', 'signed_id', 'bring_id', 'executor_id',  'creator_id', 'last_edit_id',
                'nomenclature_id', 'type', 'state', 'organizer_id' , 'eventWay','eventLevel' ,'minister','minAge', 'maxAge' ,
                'purpose' ,'docEvent', 'respPeopleInfo', 'timeProvisionDay', 'extraRespInsert', 'timeInsertDay', 'extraRespMethod', 'extraRespInfoStuff'], 'integer'],
            [['order_date'], 'safe'],
            [['order_number', 'order_name'], 'string', 'max' => 256],
            [['key_words', 'keyEventWords', 'documentDetails'], 'string', 'max' => 512],
            [['eventName' ,'dateBegin', 'dateEnd', 'city'], 'string'],
            [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt']
        ];
    }
    public function attributeLabels(){
        return array_merge(parent::attributeLabels(), [
            'typeActParticipant' => 'Личный тип участия',
            'order_date' => 'Дата приказа',
            'dateBegin' => 'Дата начала',
            'eventName' => 'Название мероприятия',
            'dateEnd' => 'Дата окончания',
            'bring_id' => 'Проект вносит',
            'executor_id' => 'Кто исполняет',
            'purpose' => 'Уставная цель',
            'docEvent' => 'Документ о мероприятии',
            'respPeopleInfo' => 'Ответственный за сбор и предоставление информации',
            'timeProvisionDay' => 'Срок предоставления информации (в днях)',
            'extraRespInsert' => 'Ответственный за внесение в ЦСХД',
            'timeInsertDay' => 'Срок внесения информации (в днях)',
            'extraRespMethod' => 'Ответственный за методологический контроль',
            'extraRespInfoStuff' => 'Ответственный за информирование работников'
        ]);
    }
    public static function fill(
        OrderEventWork $modelOrderEvent = NULL,
        ForeignEventWork $foreignEvent = NULL
    )
    {
        $entity = new static();
        $entity->order_copy_id = $modelOrderEvent->order_copy_id;
        $entity->order_number = $modelOrderEvent->order_number;
        $entity->order_postfix = $modelOrderEvent->order_postfix;
        $entity->order_date = $modelOrderEvent->order_date;
        $entity->order_name = $modelOrderEvent->order_name;
        $entity->signed_id = $modelOrderEvent->signed_id;
        $entity->bring_id = $modelOrderEvent->bring_id;
        $entity->executor_id = $modelOrderEvent->executor_id;
        $entity->key_words = $modelOrderEvent->key_words;
        $entity->creator_id = $modelOrderEvent->creator_id;
        $entity->last_edit_id = $modelOrderEvent->last_edit_id;
        //$entity->target = $modelOrderEvent->target;
        $entity->type = $modelOrderEvent->type;
        $entity->state = $modelOrderEvent->state;
        $entity->nomenclature_id = $modelOrderEvent->nomenclature_id;
        $entity->study_type = $modelOrderEvent->study_type;
// карточка мероприятия
        $entity->eventName = $foreignEvent->name;
        $entity->organizer_id = $foreignEvent->organizer_id;
        $entity->dateBegin = $foreignEvent->begin_date;
        $entity->dateEnd = $foreignEvent->end_date;
        $entity->city = $foreignEvent->city;
        $entity->minister = $foreignEvent->minister;
        $entity->minAge = $foreignEvent->min_age;
        $entity->maxAge = $foreignEvent->max_age;
        $entity->eventWay = $foreignEvent->format;
        $entity->eventLevel = $foreignEvent->level;
        $entity->keyEventWords = $foreignEvent->key_words;
// Дополнительная информация для генерации приказа
        /*
        $entity->purpose;
        $entity->docEvent;
        $entity->respPeopleInfo;
        $entity->timeProvisionDay;
        $entity->extraRespInsert;
        $entity->timeInsertDay;
        $entity->extraRespMethod;
        $entity->extraRespInfoStuff;
// награды и номинации
        $entity->team;
        $entity->award;
        $entity->teams;
        $entity->awards;
        $entity->participant_id;
        $entity->branch;
        $entity->teacher_id;
        $entity->teacher2_id;
        $entity->focus;
        $entity->formRealization;
        $entity->teamList;
        $entity->nominationList;

        $entity->scanFile;
        $entity->docFiles;
        $entity->actFiles;
        */
        return $entity;
    }
    public function fillExtraInfo(OrderEventGenerateWork $model = NULL){
        $this->purpose = $model->purpose;
        $this->docEvent = $model->doc_event;
        $this->respPeopleInfo = (PeopleStampWork::findOne($model->resp_people_info_id))->people_id;
        $this->timeProvisionDay = $model->time_provision_day;
        $this->extraRespInsert = (PeopleStampWork::findOne($model->extra_resp_insert_id))->people_id;
        $this->timeInsertDay = $model->time_insert_day;
        $this->extraRespMethod = (PeopleStampWork::findOne($model->extra_resp_method_id))->people_id;
        $this->extraRespInfoStuff = (PeopleStampWork::findOne($model->extra_resp_info_stuff_id))->people_id;
        $this->documentDetails = $model->document_details;

    }
    public function setValuesForUpdate()
    {
        $this->bring_id = (PeopleStampWork::findOne($this->bring_id))->people_id;
        $this->executor_id = (PeopleStampWork::findOne($this->executor_id))->people_id;
        $this->signed_id = (PeopleStampWork::findOne($this->signed_id))->people_id;
    }
}