<?php

namespace frontend\models\work\event;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\models\scaffold\Event;
use common\models\work\UserWork;
use common\repositories\event\EventRepository;
use common\repositories\regulation\RegulationRepository;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderMainWork;
use frontend\models\work\regulation\RegulationWork;
use InvalidArgumentException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @property UserWork $creatorWork */
/** @property PeopleStampWork $responsible1Work */
/** @property PeopleStampWork $responsible2Work */
/** @property OrderMainWork $orderWork */
/** @property UserWork $creatorWork */
/** @property UserWork $lastEditorWork */
/** @property EventBranchWork[] $eventBranchWorks */

class EventWork extends Event
{
    use EventTrait;

    /**
     * Имена файлов для сохранения в БД
     */
    public $protocolExist;
    public $reportExist;
    public $photoExist;
    public $otherExist;

    /**
     * Переменные для input-file в форме
     */
    public $protocolFiles;
    public $reportingFiles;
    public $photoFiles;
    public $otherFiles;

    public $scopes;
    public $branches;

    public $isTechnopark;
    public $isQuantorium;
    public $isCDNTT;
    public $isMobQuant;
    public $isCod;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
                [['name', 'start_date', 'finish_date', 'address'], 'required'],
                [['scopes', 'branches'], 'safe'],
                ['child_rst_participants_count', 'compare', 'compareAttribute' => 'child_participants_count', 'operator' => '<=', 'message' => 'Количество детей от РШТ не должно превышать общего количества детей'],
            ]
        );
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Название мероприятия',
            'datePeriod' => 'Период проведения',
            'eventType' => 'Тип мероприятия',
            'scopesSplitter' => 'Тематическая направленность',
            'responsibleString' => 'Ответственный(-ые) работник(-и)',
            'eventBranches' => 'Мероприятие проводит',
            'regulationRaw' => 'Положение',
            'address' => 'Адрес проведения',
            'eventLevel' => 'Уровень мероприятия',
            'participantCount' => 'Кол-во участников',
            'isFederal' => 'Входит в ФП',
            'orderNameRaw' => 'Приказ',
            'eventWay' => 'Формат проведения',
            'eventLevelAndType' => 'Уровень и Тип мероприятия',
            'childParticipantsCount' => 'Кол-во детей',
            'childRSTParticipantsCount' => 'Кол-во детей из РШТ',
            'teacherParticipantsCount' => 'Кол-во педагогов',
            'otherParticipantsCount' => 'Кол-во иных',
            'ageRestrictions' => 'Возрастной диапазон',
            'eventGroupRaw' => 'Связанные группы',
            'age_left_border' => 'Возраст детей: минимальный, лет',
            'age_right_border' => 'Возраст детей: максимальный, лет',
            'child_participants_count' => 'Кол-во детей',
            'child_rst_participants_count' => 'В т.ч. обучающихся РШТ',
            'teacher_participants_count' => 'Кол-во педагогов',
            'other_participants_count' => 'Кол-во иных',
            'key_words' => 'Ключевые слова',
            'keyWords' => 'Ключевые слова',
            'comment' => 'Примечание',
            'protocolFiles' => 'Протокол мероприятия',
            'photoFiles' => 'Фотоматериалы',
            'reportingFiles' => 'Явочные документы',
            'otherFiles' => 'Другие файлы',
        ]);
    }

    public function getDatePeriod() {
        return DateFormatter::format($this->start_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot)
            . ' - ' . DateFormatter::format($this->finish_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
    }

    public function getEventType()
    {
        return Yii::$app->eventType->get($this->event_type);
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getEventLevel()
    {
        return Yii::$app->eventLevel->get($this->event_level);
    }

    public function getParticipantCount()
    {
        return $this->getChildParticipantsCount() + $this->getTeacherParticipantsCount() + $this->getOtherParticipantsCount();
    }

    public function getChildParticipantsCount()
    {
        return $this->child_participants_count;
    }

    public function getChildRSTParticipantsCount()
    {
        return $this->child_rst_participants_count;
    }

    public function getTeacherParticipantsCount()
    {
        return $this->teacher_participants_count;
    }

    public function getOtherParticipantsCount()
    {
        return $this->other_participants_count;
    }

    public function getAgeRestrictions()
    {
        return $this->age_left_border . ' - ' . $this->age_right_border . ' лет';
    }

    public function getOrderNameRaw()
    {
        $order = $this->orderWork;
        return $order ?
                StringFormatter::stringAsLink("Приказ № {$order->getFullName()}", Url::to([Yii::$app->frontUrls::ORDER_MAIN_VIEW, 'id' => $order->id])) :
                "Нет";
    }

    public function getEventGroupRaw()
    {
        $result = '';
        $eventGroups = $this->eventTrainingGroupWork;

        if (!$eventGroups) {
            $result = '-----';
        }
        foreach ($eventGroups as $eventGroup) {
            $trainingGroup = $eventGroup->trainingGroup;
            $result .= StringFormatter::stringAsLink("{$trainingGroup->number}", Url::to([Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $trainingGroup->id])) . ', ';
        }

        return substr($result, 0, -2);
    }

    public function getKeyWord()
    {
        return $this->key_words ? $this->key_words : '---';
    }

    public function getEventWay()
    {
        return Yii::$app->eventWay->get($this->event_way);
    }

    public function getEventForm()
    {
        return Yii::$app->eventForm->get($this->event_form);
    }

    public function getEventLevelAndType()
    {
        return $this->getEventLevel() . '<br>' . $this->getEventType();
    }

    public function getIsFederal()
    {
        return $this->is_federal == 1 ? 'Да' : 'Нет';
    }

    public function getScopesString()
    {
        $eventScopes = (Yii::createObject(EventRepository::class))->getScopes($this->id);

        $result = '';
        $scopes = ArrayHelper::getColumn($eventScopes, 'participation_scope');
        foreach ($scopes as $scope) {
            $result .= Yii::$app->participationScope->get($scope) . ', ';
        }

        return substr($result, 0, -2);
    }

    public function getContainsEducation()
    {
        return $this->contains_education == 0 ? 'Нет' : 'Да';
    }

    public function getComment()
    {
        return $this->comment ? $this->comment : '---';
    }

    public function getResponsible1Work()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'responsible1_id']);
    }

    public function getResponsible2Work()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'responsible2_id']);
    }

    public function getEventBranches()
    {
        $eventBranches = (Yii::createObject(EventRepository::class))->getBranches($this->id);

        $result = '';
        $branches = ArrayHelper::getColumn($eventBranches, 'branch');
        foreach ($branches as $branch) {
            $result .= Yii::$app->branches->get($branch) . ', ';
        }

        return substr($result, 0, -2);
    }

    public function getRegulationRaw()
    {
        $regulation = (Yii::createObject(RegulationRepository::class))->get($this->regulation_id);

        return $regulation ?
            StringFormatter::stringAsLink("Положение '$regulation->name'", Url::to([Yii::$app->frontUrls::REG_VIEW, 'id' => $regulation->id])) :
            'Нет';
    }

    public function getResponsibles()
    {
        $resbonsibles = [];
        if ($this->responsible1_id) {
            $resbonsibles[] = StringFormatter::stringAsLink(
                    $this->responsible1Work->peopleWork->getFio(PeopleWork::FIO_SURNAME_INITIALS),
                    Url::to([Yii::$app->frontUrls::PEOPLE_VIEW, 'id' => $this->responsible1Work->people_id]));
        }
        if ($this->responsible2_id) {
            $resbonsibles[] = StringFormatter::stringAsLink(
                    $this->responsible2Work->peopleWork->getFio(PeopleWork::FIO_SURNAME_INITIALS),
                    Url::to([Yii::$app->frontUrls::PEOPLE_VIEW, 'id' => $this->responsible2Work->people_id]));
        }

        return implode('<br>', $resbonsibles);
    }

    /**
     * Возвращает массив
     * link => форматированная ссылка на документ
     * id => ID записи в таблице files
     * @param $filetype
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_PROTOCOL:
                $addPath = FilesHelper::createAdditionalPath(EventWork::tableName(), FilesHelper::TYPE_PROTOCOL);
                break;
            case FilesHelper::TYPE_PHOTO:
                $addPath = FilesHelper::createAdditionalPath(EventWork::tableName(), FilesHelper::TYPE_PHOTO);
                break;
            case FilesHelper::TYPE_REPORT:
                $addPath = FilesHelper::createAdditionalPath(EventWork::tableName(), FilesHelper::TYPE_REPORT);
                break;
            case FilesHelper::TYPE_OTHER:
                $addPath = FilesHelper::createAdditionalPath(EventWork::tableName(), FilesHelper::TYPE_OTHER);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    public function beforeSave($insert)
    {
        if ($this->creator_id == null) {
            $this->creator_id = Yii::$app->user->identity->getId();
        }
        $this->last_edit_id = Yii::$app->user->identity->getId();

        return parent::beforeSave($insert); 
    }

    public function beforeValidate()
    {
        if ($this->order_id == '') $this->order_id = null;
        if ($this->regulation_id == '') $this->regulation_id = null;
        $this->start_date = DateFormatter::format($this->start_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->finish_date = DateFormatter::format($this->finish_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); 
    }

    public function fillSecondaryFields()
    {
        $branches = ArrayHelper::getColumn((Yii::createObject(EventRepository::class))->getBranches($this->id), 'branch');
        $scopes = ArrayHelper::getColumn((Yii::createObject(EventRepository::class))->getScopes($this->id), 'participation_scope');

        $branchArray = [];
        foreach (array_keys(Yii::$app->branches->getList()) as $branch) {
            if (in_array($branch, $branches)) {
                $branchArray[] = $branch;
            }
        }

        $scopesArray = [];
        foreach (array_keys(Yii::$app->participationScope->getList()) as $scope) {
            if (in_array($scope, $scopes)) {
                $scopesArray[] = $scope;
            }
        }

        $this->branches = $branchArray;
        $this->scopes = $scopesArray;
    }

    public function setValuesForUpdate()
    {
        $this->responsible1_id = $this->responsible1Work->people_id;
        $this->responsible2_id = $this->responsible2Work->people_id;
    }

    public function getOrderWork()
    {
        return $this->hasOne(OrderMainWork::class, ['id' => 'order_id']);
    }

    public function getEventTrainingGroupWork()
    {
        return $this->hasMany(EventTrainingGroupWork::class, ['event_id' => 'id']);
    }

    public function getCreatorName()
    {
        $creator = $this->creatorWork;
        return $creator ? $creator->getFullName() : '---';
    }

    public function getLastEditorName()
    {
        $editor = $this->lastEditorWork;
        return $editor ? $editor->getFullName() : '---';
    }

    public function getCreatorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'creator_id']);
    }

    public function getLastEditorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'last_edit_id']);
    }

    public function checkFilesExist()
    {
        $this->reportExist = count($this->getFileLinks(FilesHelper::TYPE_REPORT)) > 0;
        $this->photoExist = count($this->getFileLinks(FilesHelper::TYPE_PHOTO)) > 0;
        $this->protocolExist = count($this->getFileLinks(FilesHelper::TYPE_PROTOCOL)) > 0;
        $this->otherExist = count($this->getFileLinks(FilesHelper::TYPE_OTHER)) > 0;
    }

    public function getFullReporting()
    {
        $link = '#';
        if ($this->reportExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_REPORT, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    public function getFullProtocol()
    {
        $link = '#';
        if ($this->protocolExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_PROTOCOL, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    public function getFullPhoto()
    {
        $link = '#';
        if ($this->photoExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_PHOTO, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    public function getFullOther()
    {
        $link = '#';
        if ($this->otherExist) {
            $link = Url::to(['get-files', 'classname' => self::class, 'filetype' => FilesHelper::TYPE_OTHER, 'id' => $this->id]);
        }

        return HtmlBuilder::createSVGLink($link);
    }

    public function getDocumentOrderWork()
    {
        return $this->hasOne(DocumentOrderWork::class, ['id' => 'order_id']);
    }

    public function getRegulationWork()
    {
        return $this->hasOne(RegulationWork::class, ['id' => 'regulation_id']);
    }

    public function getScopesWork()
    {
        return $this->hasMany(EventScopeWork::class, ['event_id' => 'id']);
    }

    public function getEventBranchWorks()
    {
        return $this->hasMany(EventBranchWork::class, ['event_id' => 'id']);
    }
}