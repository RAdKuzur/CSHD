<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "training_group_expert".
 *
 * @property int $id
 * @property int|null $expert_id
 * @property int|null $training_group_id
 * @property int|null $expert_type
 *
 * @property PeopleStamp $expert
 * @property TrainingGroup $trainingGroup
 */
class TrainingGroupExpert extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_group_expert';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['expert_id', 'training_group_id', 'expert_type'], 'integer'],
            [['expert_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['expert_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::class, 'targetAttribute' => ['training_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'expert_id' => 'Expert ID',
            'training_group_id' => 'Training Group ID',
            'expert_type' => 'Expert Type',
        ];
    }

    /**
     * Gets query for [[Expert]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpert()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'expert_id']);
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
