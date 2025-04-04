<?php

namespace common\repositories\providers\lesson_theme;

use frontend\models\work\educational\training_group\LessonThemeWork;

class LessonThemeMockProvider implements LessonThemeProviderInterface
{
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get($id)
    {
        return $this->dataStore[$id] ?? null;
    }

    public function getByTeacherIds(array $teacherIds)
    {
        return array_filter($this->data, function($item) use ($teacherIds) {
            return in_array($item['teacher_id'], $teacherIds);
        });
    }

    public function getByLessonIds(array $lessonIds)
    {
        return array_filter($this->data, function($item) use ($lessonIds) {
            return in_array($item['training_group_lesson_id'], $lessonIds);
        });
    }

    public function delete(LessonThemeWork $model)
    {
        unset($this->data[$model->id]);
        return true;
    }

    public function save(LessonThemeWork $model)
    {
        $model->id = count($this->data);
        $this->data[] = $model;
        return $model->id;
    }
}