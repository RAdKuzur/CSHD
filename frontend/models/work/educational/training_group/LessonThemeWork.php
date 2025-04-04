<?php


namespace frontend\models\work\educational\training_group;


use common\models\scaffold\LessonTheme;
use frontend\models\work\educational\training_program\ThematicPlanWork;
use frontend\models\work\general\PeopleStampWork;

/**
 * @property TrainingGroupLessonWork $trainingGroupLessonWork
 * @property ThematicPlanWork $thematicPlanWork
 * @property PeopleStampWork $teacherWork
 */
class LessonThemeWork extends LessonTheme
{
    public static function fill(
        int $trainingGroupLessonId,
        int $thematicPlanId,
        int $teacherId = null
    )
    {
        $entity = new static();
        $entity->training_group_lesson_id = $trainingGroupLessonId;
        $entity->thematic_plan_id = $thematicPlanId;
        $entity->teacher_id = $teacherId;

        return $entity;
    }

    public function rules()
    {
        return [
            [['teacher_id', 'id'], 'integer']
        ];
    }

    public function getTrainingGroupLessonWork()
    {
        return $this->hasOne(TrainingGroupLessonWork::class, ['id' => 'training_group_lesson_id']);
    }

    public function getThematicPlanWork()
    {
        return $this->hasOne(ThematicPlanWork::class, ['id' => 'thematic_plan_id']);
    }

    public function getTeacherWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'teacher_id']);
    }
}