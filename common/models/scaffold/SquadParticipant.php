<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "squad_participant".
 *
 * @property int $id
 * @property int $act_participant_id
 * @property int $participant_id
 *
 * @property ActParticipant $actParticipant
 * @property ForeignEventParticipants $participant
 */
class SquadParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'squad_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['act_participant_id', 'participant_id'], 'required'],
            [['act_participant_id', 'participant_id'], 'integer'],
            [['act_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ActParticipant::class, 'targetAttribute' => ['act_participant_id' => 'id']],
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
            'act_participant_id' => 'Act Participant ID',
            'participant_id' => 'Participant ID',
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
