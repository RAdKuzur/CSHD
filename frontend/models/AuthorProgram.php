<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "author_program".
 *
 * @property int $id
 * @property int|null $author_id
 * @property int|null $training_program_id
 *
 * @property People $author
 * @property TrainingProgram $trainingProgram
 */
class AuthorProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'author_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'training_program_id'], 'integer'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['author_id' => 'id']],
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
            'author_id' => 'Author ID',
            'training_program_id' => 'Training Program ID',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(People::class, ['id' => 'author_id']);
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
