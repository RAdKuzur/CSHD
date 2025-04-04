<?php

namespace frontend\models\work\educational\training_group;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\components\dictionaries\base\StudyStatusDictionary;
use common\helpers\DateFormatter;
use common\helpers\files\FilePaths;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\models\scaffold\TrainingGroupParticipant;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\CertificateWork;
use frontend\models\work\general\PeopleStampWork;
use Yii;
use yii\helpers\Url;

/**
 * @property ForeignEventParticipantsWork $participantWork
 * @property TrainingGroupWork $trainingGroupWork
 * @property GroupProjectThemesWork $groupProjectThemesWork
 * @property CertificateWork $certificateWork
 */

class TrainingGroupParticipantWork extends TrainingGroupParticipant
{
    private const INIT_STATUS = 0;

    public static function fill(
        $groupId,
        $participantId,
        $sendMethod,
        $id = null,
        $status = self::INIT_STATUS
    )
    {
        $entity = new static();
        $entity->id = $id;
        $entity->training_group_id = $groupId;
        $entity->participant_id = $participantId;
        $entity->send_method = $sendMethod;
        $entity->status = $status;

        return $entity;
    }
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['id', 'integer']
        ]);
    }

    public function __toString()
    {
        return "[ParticipantID: $this->participant_id][GroupID: $this->training_group_id][SendMethod: $this->send_method]";
    }

    public function getParticipantWork()
    {
        return $this->hasOne(ForeignEventParticipantsWork::class, ['id' => 'participant_id']);
    }

    public function getTrainingGroupWork()
    {
        return $this->hasOne(TrainingGroupWork::class, ['id' => 'training_group_id']);
    }

    /**
     * Выводит инфу о статусе обучающегося через подсказчик и иконку
     * @return string
     */
    public function getRawStatus()
    {
        $stringStatus = '<b>' . Yii::$app->studyStatus->get($this->status) . '</b>';
        if ($this->status == Yii::$app->studyStatus::ACTIVE || $this->status == Yii::$app->studyStatus::TRANSFER_IN) {
            $svgColor = HtmlBuilder::SVG_PRIMARY_COLOR;
        } else if ($this->status == Yii::$app->studyStatus::DEDUCT || $this->status == Yii::$app->studyStatus::TRANSFER_OUT) {
            $svgColor = HtmlBuilder::SVG_CRITICAL_COLOR;
        } else {
            $svgColor = '';
        }
        return HtmlBuilder::createTooltipIcon($stringStatus, FilePaths::SVG_STATUS, $svgColor);
    }

    /**
     * Информация о необходимости блокировать изменение явок в журнале
     * @return bool
     */
    public function isBlockedJournal()
    {
        if ($this->status == Yii::$app->studyStatus::DEDUCT || $this->status == Yii::$app->studyStatus::TRANSFER_OUT) {
            return true;
        }
        return false;
    }

    /**
     * Выводит инфу о сертификате через подсказчик и иконку
     * @return string
     */
    public function getRawCertificate()
    {
        if ($certificate = $this->certificateWork) {
            $string = '<b>Сертификат № ' . $certificate->getCertificateLongNumber() . ' ' . $certificate->getPrettyStatus() . '</b>';
            if ($certificate->isSend()) {
                $svgColor = HtmlBuilder::SVG_PRIMARY_COLOR;
            } else {
                $svgColor = HtmlBuilder::SVG_CRITICAL_COLOR;
            }
            return HtmlBuilder::createTooltipIcon($string, FilePaths::SVG_CERTIFICATE, $svgColor);
        }
        return '';
    }

    public function getCertificateWork()
    {
        return $this->hasOne(CertificateWork::class, ['training_group_participant_id' => 'id']);
    }

    public function getFullFio()
    {
        $model = ForeignEventParticipantsWork::findOne($this->participant_id);
        return $model->getFullFio();
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function getActivity($orderId){
        if($this->id != NULL && $orderId != NULL) {
            if (
                OrderTrainingGroupParticipantWork::find()
                    ->andWhere(['training_group_participant_in_id' => $this->id])
                    ->andWhere(['order_id' => $orderId])
                    ->count() +
                OrderTrainingGroupParticipantWork::find()
                    ->andWhere(['training_group_participant_out_id' => $this->id])
                    ->andWhere(['order_id' => $orderId])
                    ->count() > 0
            ) {
                return 1;
            }
        }
        return 0;
    }

    public function getActualGroup($modelId)
    {
        if($modelId != NULL) {
            $orderParticipant = OrderTrainingGroupParticipantWork::find()
                ->andWhere(['training_group_participant_out_id' => $this->id])
                ->andWhere(['order_id' => $modelId])
                ->one();
            if($orderParticipant == NULL) {
                return $this->training_group_id;
            }
            $participant = TrainingGroupParticipantWork::find()->andWhere(['id' => $orderParticipant->training_group_participant_in_id])->one();
            return $participant->training_group_id;
        }
        else {
            return $this->training_group_id;
        }
    }
    // NULL - для подробной информации
    // not NULL - для информации из справочника
    public function getFullStatusInfo($type = NULL){
        $linkIn = OrderTrainingGroupParticipantWork::find()
            ->andWhere(['training_group_participant_in_id' => $this->id])
            ->andWhere(['<>' ,'training_group_participant_out_id' , ''])
            ->one();
        $linkOut = OrderTrainingGroupParticipantWork::find()
            ->andWhere(['training_group_participant_out_id' => $this->id])
            ->andWhere(['<>' ,'training_group_participant_in_id' , ''])
            ->one();
        switch ($this->status) {
            case NomenclatureDictionary::ORDER_INIT:
                return is_null($type) ? 'Не зачислен' : Yii::$app->studyStatus->get(StudyStatusDictionary::INACTIVE);
            case NomenclatureDictionary::ORDER_ENROLL;
                if (is_null($linkIn)) {
                    return  is_null($type) ? 'Состоит в группе' . ' ' . $this->trainingGroupWork->number : Yii::$app->studyStatus->get(StudyStatusDictionary::ACTIVE);
                }
                else  {
                    $participant = TrainingGroupParticipantWork::findOne($linkIn->training_group_participant_out_id);
                    return is_null($type) ? 'Состоит в группе ' . $this->trainingGroupWork->number. ' Переведён из группы ' . $participant->trainingGroupWork->number . ' в группу ' . $this->trainingGroupWork->number : Yii::$app->studyStatus->get(StudyStatusDictionary::TRANSFER_IN);
                }
            case NomenclatureDictionary::ORDER_DEDUCT:
                if (is_null($linkOut)) {
                    return is_null($type) ? 'Отчислен из группы ' . $this->trainingGroupWork->number : Yii::$app->studyStatus->get(StudyStatusDictionary::DEDUCT);
                }
                else
                {
                    $participant = TrainingGroupParticipantWork::findOne($linkOut->training_group_participant_in_id);
                    return is_null($type) ? 'Не состоит в группе ' . $this->trainingGroupWork->number . ' Переведён из группы ' . $this->trainingGroupWork->number. ' в группу ' .  $participant->trainingGroupWork->number : Yii::$app->studyStatus->get(StudyStatusDictionary::TRANSFER_OUT);
                }
            default:
                return Yii::$app->studyStatus->get(StudyStatusDictionary::ERROR);
        }
    }

    public function setParticipantId(int $participantId)
    {
        $this->participant_id = $participantId;
    }

    /**
     * Возвращает строку для вывода в карточке участника деятельности
     *
     * @return string
     */
    public function getFullGroupString()
    {
        $result = '<span class="badge badge-secondary">' . Yii::$app->branches->get($this->trainingGroupWork->branch) . '</span>';
        $startDate = DateFormatter::format($this->trainingGroupWork->start_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
        $endDate = DateFormatter::format($this->trainingGroupWork->finish_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
        $result .= " $startDate - $endDate | ";
        $result .= StringFormatter::stringAsLink(
            $this->trainingGroupWork->number,
            Url::to([Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $this->training_group_id])
        );

        return $result;
    }

    public function getGroupProjectThemesWork()
    {
        return $this->hasOne(GroupProjectThemesWork::class, ['id' => 'group_project_themes_id']);
    }

    public function getCertificatesWork()
    {
        return $this->hasOne(CertificateWork::class, ['training_group_participant_id' => 'id']);
    }
}