<?php

namespace common\repositories\providers\teacher_group;

use frontend\models\work\educational\training_group\TeacherGroupWork;

interface TeacherGroupProviderInterface
{
    public function getAll();
    public function getAllTeachersFromGroup($groupId);
    public function getAllFromTeacherIds(array $teacherIds);
    public function save(TeacherGroupWork $model);
}