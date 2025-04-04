<?php

namespace common\repositories\educational;

use common\repositories\providers\teacher_group\TeacherGroupProvider;
use common\repositories\providers\teacher_group\TeacherGroupProviderInterface;
use DomainException;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use Yii;

class TeacherGroupRepository
{
    private $provider;

    public function __construct(
        TeacherGroupProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(TeacherGroupProvider::class);
        }

        $this->provider = $provider;
    }

    public function getAll()
    {
        return $this->provider->getAll();
    }

    public function getAllTeachersFromGroup($groupId)
    {
        return $this->provider->getAllTeachersFromGroup($groupId);
    }

    public function getAllFromTeacherIds(array $teacherIds)
    {
        return $this->provider->getAllFromTeacherIds($teacherIds);
    }

    public function prepareCreate($teacherId, $groupId)
    {
        if (get_class($this->provider) == TeacherGroupProvider::class) {
            return $this->provider->prepareCreate($teacherId, $groupId);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareCreate');
        }
    }

    public function prepareDelete($id)
    {
        if (get_class($this->provider) == TeacherGroupProvider::class) {
            return $this->provider->prepareDelete($id);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareDelete');
        }
    }
}