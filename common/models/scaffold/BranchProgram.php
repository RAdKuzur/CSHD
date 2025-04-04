<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "branch_program".
 *
 * @property int $id
 * @property int|null $branch
 * @property int|null $training_program_id
 *
 * @property TrainingProgram $trainingProgram
 */
class BranchProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'branch_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch', 'training_program_id'], 'integer'],
            [['training_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingProgram::class, 'targetAttribute' => ['training_program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch' => 'Branch',
            'training_program_id' => 'Training Program ID',
        ];
    }

    /**
     * Gets query for [[TrainingProgram]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingProgram()
    {
        return $this->hasOne(TrainingProgram::class, ['id' => 'training_program_id']);
    }
}
