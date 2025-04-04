<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "participant_achievement".
 *
 * @property int $id
 * @property int|null $act_participant_id
 * @property string|null $achievement
 * @property int|null $type
 * @property string|null $cert_number
 * @property string|null $nomination
 * @property string|null $date
 *
 * @property ActParticipant $actParticipant
 */
class ParticipantAchievement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'participant_achievement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['act_participant_id', 'type'], 'integer'],
            [['date'], 'safe'],
            [['achievement'], 'string', 'max' => 1024],
            [['cert_number'], 'string', 'max' => 256],
            [['nomination'], 'string', 'max' => 512],
            [['act_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ActParticipant::class, 'targetAttribute' => ['act_participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'act_participant_id' => 'Act Participant ID',
            'achievement' => 'Achievement',
            'type' => 'Type',
            'cert_number' => 'Cert Number',
            'nomination' => 'Nomination',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[ActParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActParticipant()
    {
        return $this->hasOne(ActParticipant::class, ['id' => 'act_participant_id']);
    }
}
