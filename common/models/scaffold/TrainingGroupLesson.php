<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "training_group_lesson".
 *
 * @property int $id
 * @property string|null $lesson_date
 * @property string|null $lesson_start_time
 * @property string|null $lesson_end_time
 * @property int|null $duration
 * @property int|null $branch
 * @property int|null $auditorium_id
 * @property int|null $training_group_id
 *
 * @property Auditorium $auditorium
 * @property TrainingGroup $trainingGroup
 */
class TrainingGroupLesson extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_group_lesson';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lesson_date', 'lesson_start_time', 'lesson_end_time'], 'safe'],
            [['duration', 'branch', 'auditorium_id', 'training_group_id'], 'integer'],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::class, 'targetAttribute' => ['auditorium_id' => 'id']],
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
            'lesson_date' => 'Lesson Date',
            'lesson_start_time' => 'Lesson Start Time',
            'lesson_end_time' => 'Lesson End Time',
            'duration' => 'Duration',
            'branch' => 'Branch',
            'auditorium_id' => 'Auditorium ID',
            'training_group_id' => 'Training Group ID',
        ];
    }

    /**
     * Gets query for [[Auditorium]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorium()
    {
        return $this->hasOne(Auditorium::class, ['id' => 'auditorium_id']);
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
