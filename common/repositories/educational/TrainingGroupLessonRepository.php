<?php

namespace common\repositories\educational;

use common\components\traits\CommonDatabaseFunctions;
use common\repositories\providers\group_lesson\TrainingGroupLessonProvider;
use common\repositories\providers\group_lesson\TrainingGroupLessonProviderInterface;
use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use Yii;

class TrainingGroupLessonRepository
{
    use CommonDatabaseFunctions;

    private $provider;

    public function __construct(
        TrainingGroupLessonProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(TrainingGroupLessonProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getAll()
    {
        return $this->provider->getAll();
    }

    public function getByIds($ids)
    {
        return $this->provider->getByIds($ids);
    }

    public function getLessonsFromGroup($id)
    {
        return $this->provider->getLessonsFromGroup($id);
    }

    public function prepareCreate($groupId, $lessonDate, $lessonStartTime, $branch, $auditoriumId, $lessonEndTime, $duration)
    {
        if (get_class($this->provider) == TrainingGroupLessonProvider::class) {
            return $this->provider->prepareCreate($groupId, $lessonDate, $lessonStartTime, $branch, $auditoriumId, $lessonEndTime, $duration);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareCreate');
        }
    }

    public function prepareDelete($id)
    {
        if (get_class($this->provider) == TrainingGroupLessonProvider::class) {
            return $this->provider->prepareDelete($id);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareDelete');
        }
    }

    public function delete(TrainingGroupLessonWork $model)
    {
        return $this->provider->delete($model);
    }

    public function save(TrainingGroupLessonWork $model)
    {
        return $this->provider->save($model);
    }
}