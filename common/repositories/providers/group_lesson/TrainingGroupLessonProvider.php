<?php

namespace common\repositories\providers\group_lesson;

use DomainException;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use Yii;

class TrainingGroupLessonProvider implements TrainingGroupLessonProviderInterface
{
    public function get($id)
    {
        return TrainingGroupLessonWork::find()->where(['id' => $id])->one();
    }

    public function getByIds($ids)
    {
        return TrainingGroupLessonWork::find()->where(['IN', 'id', $ids])->all();
    }

    public function getLessonsFromGroup($id)
    {
        return TrainingGroupLessonWork::find()
            ->where(['training_group_id' => $id])
            ->orderBy(['lesson_date' => SORT_ASC, 'lesson_start_time' => SORT_ASC])
            ->all();
    }

    public function prepareCreate($groupId, $lessonDate, $lessonStartTime, $branch, $auditoriumId, $lessonEndTime, $duration)
    {
        $model = TrainingGroupLessonWork::fill($groupId, $lessonDate, $lessonStartTime, $branch, $auditoriumId, $lessonEndTime, $duration);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(TrainingGroupLessonWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function delete(TrainingGroupLessonWork $model)
    {
        return $model->delete();
    }

    public function save(TrainingGroupLessonWork $lesson)
    {
        if (!$lesson->save()) {
            throw new DomainException('Ошибка сохранения связки учебной группы и занятия. Проблемы: '.json_encode($lesson->getErrors()));
        }
        return $lesson->id;
    }
}