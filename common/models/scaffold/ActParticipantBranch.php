<?php

namespace common\models\scaffold;

use common\models\scaffold\ActParticipant;
use Yii;

/**
 * This is the model class for table "act_participant_branch".
 *
 * @property int $id
 * @property int $act_participant_id
 * @property int $branch
 *
 * @property ActParticipant $actParticipant
 */
class ActParticipantBranch extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'act_participant_branch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['act_participant_id', 'branch'], 'required'],
            [['act_participant_id', 'branch'], 'integer'],
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
            'branch' => 'Branch',
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