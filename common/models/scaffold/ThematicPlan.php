<?php

namespace common\models\scaffold;

use InvalidArgumentException;
use Yii;

/**
 * This is the model class for table "thematic_plan".
 *
 * @property int $id
 * @property string|null $theme
 * @property int|null $training_program_id
 * @property int|null $control_type
 *
 * @property TrainingProgram $trainingProgram
 */
class ThematicPlan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'thematic_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_program_id', 'control_type'], 'integer'],
            [['theme'], 'string', 'max' => 256],
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
            'theme' => 'Theme',
            'training_program_id' => 'Training Program ID',
            'control_type' => 'Control Type',
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
