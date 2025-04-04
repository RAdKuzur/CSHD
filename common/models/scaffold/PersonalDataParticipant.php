<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "personal_data_participant".
 *
 * @property int $id
 * @property int|null $participant_id
 * @property int|null $personal_data
 * @property int|null $status
 *
 * @property ForeignEventParticipants $participant
 */
class PersonalDataParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'personal_data_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['participant_id', 'personal_data', 'status'], 'integer'],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::class, 'targetAttribute' => ['participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'participant_id' => 'Participant ID',
            'personal_data' => 'Personal Data',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[Participant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipant()
    {
        return $this->hasOne(ForeignEventParticipants::class, ['id' => 'participant_id']);
    }
}
