<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "certificate".
 *
 * @property int $id
 * @property int|null $certificate_number
 * @property int|null $certificate_template_id
 * @property int|null $training_group_participant_id
 * @property int|null $status
 *
 * @property CertificateTemplates $certificateTemplate
 * @property TrainingGroupParticipant $trainingGroupParticipant
 */
class Certificate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'certificate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['certificate_number', 'certificate_template_id', 'training_group_participant_id', 'status'], 'integer'],
            [['certificate_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => CertificateTemplates::class, 'targetAttribute' => ['certificate_template_id' => 'id']],
            [['training_group_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupParticipant::class, 'targetAttribute' => ['training_group_participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'certificate_number' => 'Certificate Number',
            'certificate_template_id' => 'Certificate Template ID',
            'training_group_participant_id' => 'Training Group Participant ID',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[CertificateTemplate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCertificateTemplate()
    {
        return $this->hasOne(CertificateTemplates::class, ['id' => 'certificate_template_id']);
    }

    /**
     * Gets query for [[TrainingGroupParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipant()
    {
        return $this->hasOne(TrainingGroupParticipant::class, ['id' => 'training_group_participant_id']);
    }
}
