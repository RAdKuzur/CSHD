<?php

namespace frontend\services\educational;

use common\models\scaffold\LessonTheme;
use common\models\scaffold\TrainingGroupLesson;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\services\DatabaseServiceInterface;
use frontend\events\visit\DeleteLessonFromVisitEvent;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;

class GroupLessonService implements DatabaseServiceInterface
{
    private TrainingGroupLessonRepository $repository;

    public function __construct(
        TrainingGroupLessonRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function isAvailableDelete($id)
    {
        $lessonThemes = $this->repository->checkDeleteAvailable(LessonTheme::tableName(), TrainingGroupLesson::tableName(), $id);

        return $lessonThemes;
    }

    public function delete($id)
    {
        if (count($this->isAvailableDelete($id)) > 0) {
            return false;
        }

        /** @var TrainingGroupLessonWork $model */
        $model = $this->repository->get($id);
        $model->recordEvent(
            new DeleteLessonFromVisitEvent($id, [$model]),
            TrainingGroupLessonWork::class
        );
        $model->releaseEvents();
        return $this->repository->delete($model);
    }
}