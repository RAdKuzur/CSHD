<?php

namespace common\repositories\providers\lesson_theme;

use frontend\models\work\educational\training_group\LessonThemeWork;

interface LessonThemeProviderInterface
{
    public function get($id);
    public function getByTeacherIds(array $teacherIds);
    public function save(LessonThemeWork $model);
    public function delete(LessonThemeWork $model);
}