<?php

namespace common\repositories\providers\lesson_theme;

use DomainException;
use frontend\models\work\educational\training_group\LessonThemeWork;

class LessonThemeProvider implements LessonThemeProviderInterface
{

    public function get($id)
    {
        return LessonThemeWork::find()->where(['id' => $id])->one();
    }

    public function getByTeacherIds(array $teacherIds)
    {
        return LessonThemeWork::find()->where(['IN', 'teacher_id', $teacherIds])->all();
    }

    public function getByLessonIds(array $lessonIds)
    {
        return LessonThemeWork::find()->where(['IN', 'training_group_lesson_id', $lessonIds])->all();
    }

    public function getByTrainingGroupId(int $trainingGroupId)
    {
        return LessonThemeWork::find()
            ->joinWith(['trainingGroupLessonWork trainingGroupLessonWork'])
            ->where(['IN', 'trainingGroupLessonWork.training_group_id', $trainingGroupId])
            ->all();
    }

    public function delete(LessonThemeWork $model)
    {
        return $model->delete();
    }

    public function save(LessonThemeWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения связки учебной группы и ученика. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}