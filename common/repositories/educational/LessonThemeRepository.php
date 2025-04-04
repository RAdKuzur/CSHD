<?php

namespace common\repositories\educational;

use common\repositories\providers\lesson_theme\LessonThemeProvider;
use common\repositories\providers\lesson_theme\LessonThemeProviderInterface;
use common\repositories\providers\teacher_group\TeacherGroupProvider;
use frontend\models\work\educational\training_group\LessonThemeWork;
use Yii;

class LessonThemeRepository
{
    private $provider;

    public function __construct(
        LessonThemeProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(LessonThemeProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getByTrainingGroupId(int $trainingGroupId)
    {
        return $this->provider->getByTrainingGroupId($trainingGroupId);
    }

    public function getByLessonIds(array $lessonIds)
    {
        return $this->provider->getByLessonIds($lessonIds);
    }

    public function getByTeacherIds(array $teacherIds)
    {
        return $this->provider->getByTeacherIds($teacherIds);
    }

    public function delete(LessonThemeWork $model)
    {
        return $this->provider->delete($model);
    }

    public function save(LessonThemeWork $model)
    {
        return $this->provider->save($model);
    }
}