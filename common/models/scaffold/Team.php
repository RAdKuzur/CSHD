<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "team".
 *
 * @property int $id
 * @property int $act_participant
 * @property int $foreign_event_id
 * @property int $participant_id
 * @property int $team_name_id
 */
class Team extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['act_participant', 'foreign_event_id', 'participant_id', 'team_name_id'], 'required'],
            [['act_participant', 'foreign_event_id', 'participant_id', 'team_name_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'act_participant' => 'Act Participant',
            'foreign_event_id' => 'Foreign Event ID',
            'participant_id' => 'Participant ID',
            'team_name_id' => 'Team Name ID',
        ];
    }
}
