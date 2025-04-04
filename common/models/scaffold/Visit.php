<?php

namespace common\models\scaffold;

use Yii;

/**
 * This is the model class for table "visit".
 *
 * @property int $id
 * @property int|null $training_group_participant_id
 * @property string|null $lessons
 *
 * @property TrainingGroupParticipant $groupParticipant
 */
class Visit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_group_participant_id'], 'integer'],
            [['lessons'], 'string'],
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
            'lessons' => 'Lessons',
        ];
    }

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipant()
    {
        return $this->hasOne(TrainingGroupParticipant::class, ['id' => 'training_group_participant_id']);
    }
}
