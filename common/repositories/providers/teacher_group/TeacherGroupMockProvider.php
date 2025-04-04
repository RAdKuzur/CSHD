<?php

namespace common\repositories\providers\teacher_group;

use frontend\models\work\educational\training_group\TeacherGroupWork;

class TeacherGroupMockProvider implements TeacherGroupProviderInterface
{
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getAll()
    {
        return $this->data;
    }

    public function getAllTeachersFromGroup($groupId)
    {
        return array_filter($this->data, function($item) use ($groupId) {
            return $item['training_group_id'] === $groupId;
        });
    }

    public function getAllFromTeacherIds(array $teacherIds)
    {
        return array_filter($this->data, function($item) use ($teacherIds) {
            return in_array($item['teacher_id'], $teacherIds);
        });
    }

    public function save(TeacherGroupWork $model)
    {
        $model->id = count($this->data);
        $this->data[] = $model;
        return $model->id;
    }
}