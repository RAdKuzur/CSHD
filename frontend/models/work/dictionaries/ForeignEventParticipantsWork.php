<?php

namespace frontend\models\work\dictionaries;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilePaths;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\models\scaffold\ForeignEventParticipants;
use common\models\scaffold\PersonalDataParticipant;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\event\ParticipantAchievementRepository;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\team\ActParticipantWork;
use InvalidArgumentException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
* @property PersonalDataParticipantWork $personalDataParticipantWork
*/

class ForeignEventParticipantsWork extends ForeignEventParticipants implements PersonInterface
{
    use EventTrait;

    /**
     * DROP_CORRECT_HARD - сброс флагов true и guaranteed_true
     * DROP_CORRECT_SOFT - сброс флага true
     */
    const DROP_CORRECT_HARD = 0;
    const DROP_CORRECT_SOFT = 1;

    // Список запрещенных к разглашению ПД
    public $pd;

    /**
     * Сведения о разглашении ПД @see PersonalDataParticipantWork
     * @var mixed|null
     */
    public $personalData;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    public static function fill(
        $firstname,
        $surname,
        $birthdate,
        $email,
        $sex,
        $patronymic = ''
    )
    {
        $entity = new static();
        $entity->firstname = $firstname;
        $entity->surname = $surname;
        $entity->birthdate = $birthdate;
        $entity->email = $email;
        $entity->sex = $sex;
        $entity->patronymic = $patronymic;

        return $entity;
    }

    public function setProperties(
        $firstname,
        $surname,
        $birthdate,
        $email,
        $sex,
        $patronymic = '',
        $guaranteedTrue = 0
    )
    {
        $this->firstname = $firstname;
        $this->surname = $surname;
        $this->birthdate = $birthdate;
        $this->email = $email;
        $this->sex = $sex;
        $this->patronymic = $patronymic;
        $this->guaranteed_true = $guaranteedTrue;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'pd' => 'Запретить разглашение персональных данных',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['pd'], 'safe'],
            [['firstname', 'surname', 'email', 'sex'], 'required']
        ]);
    }

    public function getFIO(int $type): string
    {
        switch ($type) {
            case self::FIO_FULL:
                return $this->getFullFio();
            case self::FIO_SURNAME_INITIALS:
                return $this->getSurnameInitials();
            default:
                throw new InvalidArgumentException('Неизвестный тип вывода ФИО');
        }
    }

    public function getFullFio()
    {
        return "$this->surname $this->firstname $this->patronymic";
    }

    public function getSurnameInitials()
    {
        return $this->surname
            . ' ' . mb_substr($this->firstname, 0, 1)
            . '. ' . ($this->patronymic ? mb_substr($this->patronymic, 0, 1) . '.' : '');
    }

    public function fillPersonalDataRestrict(array $data)
    {
        $this->pd = [];
        if (count($data) > 0) {
            foreach ($data as $one) {
                /** @var PersonalDataParticipantWork $one */
                if ($one->isRestrict()) {
                    $this->pd[] = $one->personal_data;
                }
            }
        }
    }

    public function isTrueAnyway()
    {
        return $this->id === null || $this->is_true === 1 || $this->guaranteed_true === 1;
    }

    public function isGuaranteedTrue()
    {
        return $this->guaranteed_true === 1;
    }

    public function getSexString()
    {
        switch ($this->sex) {
            case 0:
                return 'Мужской';
            case 1:
                return 'Женский';
            default:
                return 'Другое';
        }
    }

    /**
     * Выводит информацию о персональных данных учащегося через подсказчик и иконку
     * @return string
     */
    public function createRawDisclosurePDProhibited()
    {
        $result = [];
        foreach ($this->personalDataParticipantWork as $pd) {
            /** @var PersonalDataParticipantWork $pd */
            if ($pd->isRestrict())
            {
                $result[] = Yii::$app->personalData->get($pd->personal_data);
            }
        }

        if (count($result)) {
            $content = '<b>Запрещено</b> к разглашению: <br> • ';
            $content .= implode('<br> • ', $result);
            $svgColor = HtmlBuilder::SVG_CRITICAL_COLOR;
        } else {
            $content = '<b>Запретов на разглашение ПД нет</b>';
            $svgColor = HtmlBuilder::SVG_PRIMARY_COLOR;
        }
        return HtmlBuilder::createTooltipIcon($content, FilePaths::SVG_PERSONAL_DATE, $svgColor);
    }

    public function createRawPersonalData()
    {
        return HtmlBuilder::createPersonalDataTable($this->pd);
    }

    public function setNotTrue($type = self::DROP_CORRECT_HARD)
    {
        if (!$this->isGuaranteedTrue() && $type !== self::DROP_CORRECT_HARD) {
            $this->is_true = 0;
            if (self::DROP_CORRECT_HARD) {
                $this->guaranteed_true = 0;
            }
        }
    }

    public function beforeValidate()
    {
        $this->birthdate = DateFormatter::format($this->birthdate, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); 
    }

    public function isMale()
    {
        return $this->sex === 'Мужской';
    }

    public function isFemale()
    {
        return $this->sex === 'Женский';
    }

    public function getPrettyAchieves()
    {
        $result = [];
        /** @var ParticipantAchievementWork[] $achieves */
        $achieves = (Yii::createObject(ParticipantAchievementRepository::class))->getByParticipantId($this->id);
        foreach ($achieves as $achieve) {
            $result[] = HtmlBuilder::createSubtitleAndClarification(
                $achieve->achievement . ' - ' .
                StringFormatter::stringAsLink(
                    $achieve->actParticipantWork->foreignEventWork->name,
                    Url::to([Yii::$app->frontUrls::FOREIGN_EVENT_VIEW, 'id' => $achieve->actParticipantWork->foreign_event_id])
                ),
                " ({$achieve->actParticipantWork->foreignEventWork->end_date})"
            );
        }

        return HtmlBuilder::arrayToAccordion($result);
    }

    public function getPrettyEvents()
    {
        $result = [];
        /** @var ActParticipantWork[] $acts */
        $acts = (Yii::createObject(ActParticipantRepository::class))->getByParticipantId($this->id);
        foreach ($acts as $act) {
            $result[] = HtmlBuilder::createSubtitleAndClarification(
                StringFormatter::stringAsLink(
                    $act->foreignEventWork->name,
                    Url::to([Yii::$app->frontUrls::FOREIGN_EVENT_VIEW, 'id' => $act->foreign_event_id])
                ),
                ''
            );
        }

        return HtmlBuilder::arrayToAccordion($result);
    }

    public function getPrettyGroups()
    {
        $result = [];
        /** @var TrainingGroupParticipantWork[] $participants */
        $participants = (Yii::createObject(TrainingGroupParticipantRepository::class))->getByParticipantIds([$this->id]);
        foreach ($participants as $participant) {
            $result[] = HtmlBuilder::createSubtitleAndClarification(
                $participant->getFullGroupString(),
                ''
            );
        }

        return HtmlBuilder::arrayToAccordion($result);
    }

    public function getPrettyPersonals()
    {
        $result = [];
        /** @var PersonalDataParticipantWork[] $personals */
        $personals = (Yii::createObject(PersonalDataParticipantRepository::class))->getPersonalDataByParticipantId($this->id);
        foreach ($personals as $personal) {
            $result[] = HtmlBuilder::createSubtitleAndClarification(
                $personal->getPrettyStringStatus() . ' ',
                Yii::$app->personalData->get($personal->personal_data)
            );
        }

        return HtmlBuilder::arrayToAccordion($result);
    }

    public function getPersonalDataParticipantWork()
    {
        return $this->hasMany(PersonalDataParticipantWork::class, ['participant_id' => 'id']);
    }
}
