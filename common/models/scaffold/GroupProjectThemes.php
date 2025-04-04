<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "group_project_themes".
 *
 * @property int $id
 * @property int|null $training_group_id
 * @property int|null $project_theme_id
 * @property int|null $confirm
 *
 * @property ProjectTheme $projectTheme
 * @property TrainingGroup $trainingGroup
 * @property TrainingGroupParticipant[] $trainingGroupParticipants
 */
class GroupProjectThemes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_project_themes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_group_id', 'project_theme_id', 'confirm'], 'integer'],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::class, 'targetAttribute' => ['training_group_id' => 'id']],
            [['project_theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectTheme::class, 'targetAttribute' => ['project_theme_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_group_id' => 'Training Group ID',
            'project_theme_id' => 'Project Theme ID',
            'confirm' => 'Confirm',
        ];
    }

    /**
     * Gets query for [[ProjectTheme]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectTheme()
    {
        return $this->hasOne(ProjectTheme::class, ['id' => 'project_theme_id']);
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

    /**
     * Gets query for [[TrainingGroupParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipants()
    {
        return $this->hasMany(TrainingGroupParticipant::class, ['group_project_themes_id' => 'id']);
    }
}
