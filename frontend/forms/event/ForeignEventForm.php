<?php

namespace frontend\forms\event;

use common\helpers\DateFormatter;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\order\OrderEventWork;
use frontend\models\work\team\ActParticipantWork;
use frontend\models\work\team\SquadParticipantWork;
use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\Model;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\event\ParticipantAchievementRepository;
use common\repositories\order\OrderEventRepository;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @property $id
 * @property $name
 * @property $organizer
 * @property $startDate
 * @property $endDate
 * @property $city
 * @property $format
 * @property $level
 * @property $minister
 * @property $minAge
 * @property $maxAge
 * @property OrderEventWork $orderParticipant
 * @property $addOrderParticipant
 * @property $keyWords
 * @property $orderBusinessTrip
 * @property $doc
 * @property $escort
 */
class ForeignEventForm extends Model
{
    use EventTrait;

    // Неизменяемые поля
    public ForeignEventWork $event;
    public $id;
    public $name;
    public $organizer;
    public $startDate;
    public $endDate;
    public $city;
    public $format;
    public $level;
    public $minister;
    public $minAge;
    public $maxAge;
    public OrderEventWork $orderParticipant;
    public string $docTable;

    public string $squadParticipants;

    /**
     * @var SquadParticipantWork[] $squadParticipantsModel
     * @var ParticipantAchievementWork[] $oldAchievementsModel
     * @var ActParticipantWork[] $oldAchievementsModel
     */
    public array $squadParticipantsModel;
    public array $actsParticipantModel;
    public array $oldAchievementsModel;
    public string $oldAchievements;

    // Изменяемые поля
    public $addOrderParticipant;
    public $keyWords;
    public $doc;
    public $escort;
    public $orderBusinessTrip;
    public $isBusinessTrip;
    public $lastEditId;

    /** @var ParticipantAchievementWork[] $newAchievements */
    public array $newAchievements;

    private ActParticipantRepository $actParticipantRepository;
    private ParticipantAchievementRepository $achievementRepository;
    private OrderEventRepository $orderEventRepository;
    private ForeignEventRepository $foreignEventRepository;
    private SquadParticipantRepository $squadParticipantRepository;

    public function __construct(
        $foreignEventId,
        ActParticipantRepository $actParticipantRepository = null,
        ParticipantAchievementRepository $achievementRepository = null,
        OrderEventRepository $orderEventRepository = null,
        ForeignEventRepository $foreignEventRepository = null,
        SquadParticipantRepository $squadParticipantRepository = null,
        $config = [])
    {
        parent::__construct($config);
        if (is_null($actParticipantRepository)) {
            $actParticipantRepository = Yii::createObject(ActParticipantRepository::class);
        }
        if (is_null($achievementRepository)) {
            $achievementRepository = Yii::createObject(ParticipantAchievementRepository::class);
        }
        if (is_null($orderEventRepository)) {
            $orderEventRepository = Yii::createObject(OrderEventRepository::class);
        }
        if (is_null($foreignEventRepository)) {
            $foreignEventRepository = Yii::createObject(ForeignEventRepository::class);
        }
        if (is_null($squadParticipantRepository)) {
            $squadParticipantRepository = Yii::createObject(SquadParticipantRepository::class);
        }
        $this->actParticipantRepository = $actParticipantRepository;
        $this->achievementRepository = $achievementRepository;
        $this->orderEventRepository = $orderEventRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->squadParticipantRepository = $squadParticipantRepository;

        /** @var OrderEventWork $order */
        /** @var ForeignEventWork $event */
        $event = $this->foreignEventRepository->get($foreignEventId);
        $order = $this->orderEventRepository->get($event->order_participant_id);
        $this->event = $event;
        $this->id = $foreignEventId;
        $this->name = $order->order_name;
        $this->organizer = $event->organizer_id;
        $this->startDate = $event->begin_date;
        $this->endDate = $event->end_date;
        $this->city = $event->city;
        $this->format = $event->format;
        $this->level = $event->level;
        $this->minister = $event->minister;
        $this->minAge = $event->min_age;
        $this->maxAge = $event->max_age;
        $this->orderParticipant = $order;
        $this->actsParticipantModel = $this->actParticipantRepository->getByForeignEventIds([$foreignEventId]);
        $this->squadParticipantsModel = $this->squadParticipantRepository->getAllFromEvent($foreignEventId);
        $this->squadParticipants = $this->fillActParticipants($foreignEventId);
        $this->oldAchievements = $this->fillOldAchievements($foreignEventId);
        $this->oldAchievementsModel = $this->achievementRepository->getByForeignEvent($foreignEventId);
        $this->docTable = $this->fillDocTable();

        $this->addOrderParticipant = $event->add_order_participant_id;
        $this->keyWords = $event->key_words;
        $this->escort = $event->escort_id;
        $this->orderBusinessTrip = $event->order_business_trip_id;
        $this->lastEditId = Yii::$app->user->identity->getId();
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['keyWords'], 'string'],
            [['addOrderParticipant', 'escort', 'orderBusinessTrip', 'lastEditId'], 'integer'],
        ]); // TODO: Change the autogenerated stub
    }

    public function fillActParticipants($foreignEventId)
    {
        $actIds = ArrayHelper::getColumn(
            (Yii::createObject(ActParticipantRepository::class))->getByForeignEventIds([$foreignEventId]),
            'id'
        );

        $squads = (Yii::createObject(SquadParticipantRepository::class))->getAllByActIds($actIds);
        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Участник'], ArrayHelper::getColumn($squads, 'participantWork.surnameInitials')),
                array_merge(['Отдел(-ы)'], ArrayHelper::getColumn($squads, 'participantWork.surnameInitials')),
                array_merge(['Педагог'], ArrayHelper::getColumn($squads, 'actParticipantWork.teachers')),
                array_merge(['Направленность'], ArrayHelper::getColumn($squads, 'actParticipantWork.focusName')),
                array_merge(['Номинация'], ArrayHelper::getColumn($squads, 'actParticipantWork.nomination')),
                array_merge(['Команда'], ArrayHelper::getColumn($squads, 'actParticipantWork.teamNameWork.name')),
                array_merge(['Форма реализации'], ArrayHelper::getColumn($squads, 'actParticipantWork.formName')),
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Редактировать',
                    Url::to('update-participant'),
                    [
                        'id' => ArrayHelper::getColumn($squads, 'act_participant_id'),
                        'modelId' => ArrayHelper::getColumn($squads, 'actParticipantWork.foreign_event_id')
                    ]
                ),
            ]
        );
    }

    public function fillOldAchievements($foreignEventId)
    {
        $achievements = (Yii::createObject(ParticipantAchievementRepository::class))->getByForeignEvent($foreignEventId);
        $flattenedParticipants = array_map(function ($innerArray) {
            return implode('<br>', array_map(function ($participant) {
                return $participant->participantWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS);
            }, $innerArray));
        }, ArrayHelper::getColumn($achievements, 'actParticipantWork.squadParticipantsWork'));


        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Участник'], $flattenedParticipants),
                array_merge(['Статус'], ArrayHelper::getColumn($achievements, 'actParticipantWork.prettyType')),
                array_merge(['Достижение'], ArrayHelper::getColumn($achievements, 'achievement')),
                array_merge(['Акт участия'], ArrayHelper::getColumn($achievements, 'actParticipantWork.string')),
                array_merge(['Номер сертификата'], ArrayHelper::getColumn($achievements, 'cert_number')),
                array_merge(['Дата сертификата'], ArrayHelper::getColumn($achievements, 'date')),
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Редактировать',
                    Url::to('update-achievement'),
                    [
                        'id' => ArrayHelper::getColumn($achievements, 'id'),
                        'modelId' => ArrayHelper::getColumn($achievements, 'actParticipantWork.foreign_event_id')
                    ]
                ),
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-achievement'),
                    [
                        'id' => ArrayHelper::getColumn($achievements, 'id'),
                        'modelId' => ArrayHelper::getColumn($achievements, 'actParticipantWork.foreign_event_id')
                    ]
                )
            ]
        );
    }

    public function isBusinessTrip()
    {
        return !is_null($this->orderBusinessTrip);
    }

    public function getParticipantsLink()
    {
        $mapped = array_map(function(SquadParticipantWork $item) {
            return
                StringFormatter::stringAsLink(
                    $item->participantWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS),
                    Url::to(['/dictionaries/foreign-event-participants/view', 'id' => $item->participant_id])
                ) . ' (педагог(-и): ' .
                $item->actParticipantWork->getTeachersLink() .
                ', отдел(-ы) для учета: ' .
                $item->actParticipantWork->getBranches() . ')' .
                (is_null($item->actParticipantWork->team_name_id) ? '' : " - Команда {$item->actParticipantWork->getTeamName()}");
        }, $this->squadParticipantsModel);

        return implode('<br>', $mapped);
    }

    public function getAchievementsLink()
    {
        $mapped = array_map(function(ParticipantAchievementWork $item) {
            return
                $item->getPrettyType() . ': ' .
                $item->actParticipantWork->getFormattedLinkedParticipants() . ' [' .
                $item->actParticipantWork->getBranches() . '] — ' .
                $item->achievement;
        }, $this->oldAchievementsModel);

        return implode('<br>', $mapped);
    }

    public function getAgeRange()
    {
        return "{$this->minAge} - {$this->maxAge} л.";
    }

    public function getOrderParticipant()
    {
        return StringFormatter::stringAsLink(
            $this->orderParticipant->getFullName(),
            Url::to(['/order/order-event/view', 'id' => $this->orderParticipant->id])
        );
    }

    public function getAddOrderParticipant()
    {
        /** @var OrderEventWork $order */
        $order = (Yii::createObject(OrderEventRepository::class))->get($this->addOrderParticipant);
        return $order ? StringFormatter::stringAsLink(
            $order->getFullName(),
            Url::to(['/order/order-event/view', 'id' => $order->id])
        ) : '';
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
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(ForeignEventWork::tableName(), FilesHelper::TYPE_DOC);
                break;
        }

        return FilesHelper::createFileLinks($this->event, $filetype, $addPath);
    }

    public function fillDocTable()
    {
        $docLink = $this->event->getFileLinks(FilesHelper::TYPE_DOC);
        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($docLink, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($docLink), $this->id), 'fileId' => ArrayHelper::getColumn($docLink, 'id')])
            ]
        );
    }

    public function getEventPeriod()
    {
        return
            DateFormatter::format($this->startDate, DateFormatter::Ymd_dash, DateFormatter::dmY_dot)
            . ' - ' .
            DateFormatter::format($this->endDate, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
    }

    public function save()
    {
        $this->event->add_order_participant_id = $this->addOrderParticipant;
        $this->event->key_words = $this->keyWords;
        $this->event->order_business_trip_id = $this->orderBusinessTrip;
        $this->event->last_edit_id = $this->lastEditId;
        $this->event->escort_id = $this->escort;
        $this->foreignEventRepository->save($this->event);
    }
}