<?php

namespace common\models\scaffold;

use Yii;

/**
 * This is the model class for table "lesson_theme".
 *
 * @property int $id
 * @property int|null $training_group_lesson_id
 * @property int|null $thematic_plan_id
 * @property int|null $teacher_id
 *
 * @property PeopleStamp $teacher
 * @property ThematicPlan $thematicPlan
 * @property TrainingGroupLesson $trainingGroupLesson
 */
class LessonTheme extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_theme';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_group_lesson_id', 'thematic_plan_id', 'teacher_id'], 'integer'],
            [['training_group_lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupLesson::class, 'targetAttribute' => ['training_group_lesson_id' => 'id']],
            [['thematic_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => ThematicPlan::class, 'targetAttribute' => ['thematic_plan_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['teacher_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_group_lesson_id' => 'Training Group Lesson ID',
            'thematic_plan_id' => 'Thematic Plan ID',
            'teacher_id' => 'Teacher ID',
        ];
    }

    /**
     * Gets query for [[Teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'teacher_id']);
    }

    /**
     * Gets query for [[ThematicPlan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getThematicPlan()
    {
        return $this->hasOne(ThematicPlan::class, ['id' => 'thematic_plan_id']);
    }

    /**
     * Gets query for [[TrainingGroupLesson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupLesson()
    {
        return $this->hasOne(TrainingGroupLesson::class, ['id' => 'training_group_lesson_id']);
    }
}
