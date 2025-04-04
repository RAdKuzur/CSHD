<?php

namespace frontend\models\work\educational\training_group;

use common\models\scaffold\TeacherGroup;
use frontend\models\work\general\PeopleStampWork;

/**
 * @property PeopleStampWork $teacherWork
 */

class TeacherGroupWork extends TeacherGroup
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['peopleId'], 'safe'],
        ]);
    }

    public static function fill($teacherId, $groupId, $id = -1)
    {
        $entity = new static();
        $entity->teacher_id = $teacherId;
        $entity->training_group_id = $groupId;
        if ($id != -1) {
            $entity->id = $id;
        }

        return $entity;
    }

    public function getTeacherWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'teacher_id']);
    }

    public function getPeopleId()
    {
        return $this->teacherWork->people_id;
    }

    public function setPeopleId($peopleId)
    {
        return $this->peopleId = $peopleId;
    }

    public function __toString() : string
    {
        return "[TeacherID: $this->teacher_id][GroupID: $this->training_group_id]";
    }
}