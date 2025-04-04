<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "training_group_participant".
 *
 * @property int $id
 * @property int|null $participant_id
 * @property int|null $send_method
 * @property int|null $training_group_id
 * @property int|null $status
 * @property int|null $success
 * @property int|null $points
 * @property int|null $group_project_themes_id
 *
 * @property GroupProjectThemes $groupProjectThemes
 * @property ForeignEventParticipants $participant
 * @property TrainingGroup $trainingGroup
 */
class  TrainingGroupParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_group_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['participant_id', 'send_method', 'training_group_id', 'status', 'success', 'points', 'group_project_themes_id'], 'integer'],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::class, 'targetAttribute' => ['participant_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::class, 'targetAttribute' => ['training_group_id' => 'id']],
            [['group_project_themes_id'], 'exist', 'skipOnError' => true, 'targetClass' => GroupProjectThemes::class, 'targetAttribute' => ['group_project_themes_id' => 'id']],
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
            'send_method' => 'Send Method',
            'training_group_id' => 'Training Group ID',
            'status' => 'Status',
            'success' => 'Success',
            'points' => 'Points',
            'group_project_themes_id' => 'Group Project Themes ID',
        ];
    }

    /**
     * Gets query for [[GroupProjectThemes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupProjectThemes()
    {
        return $this->hasOne(GroupProjectThemes::class, ['id' => 'group_project_themes_id']);
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

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroup()
    {
        return $this->hasOne(TrainingGroup::class, ['id' => 'training_group_id']);
    }
}
