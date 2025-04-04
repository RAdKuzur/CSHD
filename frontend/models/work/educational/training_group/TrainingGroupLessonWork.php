<?php

namespace frontend\models\work\educational\training_group;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\models\scaffold\TrainingGroupLesson;
use common\repositories\dictionaries\AuditoriumRepository;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use Yii;

/**
 * @property AuditoriumWork $auditoriumWork
 * @property TrainingGroupWork $trainingGroupWork
 */

class TrainingGroupLessonWork extends TrainingGroupLesson
{
    use EventTrait;
    public $autoDate;
    public $auditoriumName;

    public static function fill(
        $groupId,
        $lessonDate,
        $lessonStartTime,
        $branch,
        $auditoriumId,
        $lessonEndTime,
        $duration
    )
    {
        $entity = new static();
        $entity->training_group_id = $groupId;
        $entity->lesson_date = $lessonDate;
        $entity->lesson_start_time = $lessonStartTime;
        $entity->branch = $branch;
        $entity->auditorium_id = $auditoriumId;
        $entity->lesson_end_time = $lessonEndTime;
        $entity->duration = $duration;

        if ($auditoriumId !== null) {
            /** @var AuditoriumWork $auditorium */
            $auditorium = (Yii::createObject(AuditoriumRepository::class))->get($auditoriumId);
            $entity->auditoriumName = $auditorium->name . ' (' . Yii::$app->branches->get($auditorium->branch) . ')';
        }

        return $entity;
    }

    /**
     * Создает форматирование для строки одного занятия
     * в формате "d.m c H:s до H:s в ауд. N"
     * @return string
     */
    public function getLessonPrettyString()
    {
        $datePretty = DateFormatter::format($this->lesson_date, DateFormatter::Ymd_dash, DateFormatter::dm_dot);
        $lessonStartTime = DateFormatter::format($this->lesson_start_time, DateFormatter::His_colon, DateFormatter::Hi_colon);
        $lessonEndTime = DateFormatter::format($this->lesson_end_time, DateFormatter::His_colon, DateFormatter::Hi_colon);
        $auditorium = $this->auditoriumWork ? $this->auditoriumWork->getFullName() : '---';

        return "$datePretty с {$lessonStartTime} до {$lessonEndTime} в ауд. {$auditorium}";
    }

    /**
     * Проверяет, достаточно ли данных для сохранения в БД
     * @return bool
     */
    public function isEnoughData()
    {
        return $this->training_group_id !== "" &&
               $this->lesson_date !== "" &&
               $this->lesson_start_time !== "" &&
               $this->branch !== "" &&
               $this->lesson_end_time !== "" &&
               $this->duration !== "";
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['autoDate'], 'safe'],
        ]);
    }

    public function __toString()
    {
        return "[GroupID: $this->training_group_id]
                [Date: $this->lesson_date]
                [Start: $this->lesson_start_time]
                [End: $this->lesson_end_time]
                [Branch: $this->branch]
                [AudID: $this->auditorium_id]
                [Duration: $this->duration]";
    }

    public function setAuditoriumName()
    {
        if ($this->auditorium_id !== null) {
            /** @var AuditoriumWork $auditorium */
            $auditorium = (Yii::createObject(AuditoriumRepository::class))->get($this->auditorium_id);
            $this->auditoriumName = $auditorium->name . ' (' . Yii::$app->branches->get($auditorium->branch) . ')';
        }
    }
    public function getAuditoriumWork()
    {
        return $this->hasOne(AuditoriumWork::class, ['id' => 'auditorium_id']);
    }

    public function getTrainingGroupWork()
    {
        return $this->hasOne(TrainingGroupWork::class, ['id' => 'training_group_id']);
    }

    /**
     * @param VisitLesson[] $lessons
     * @return false|string
     */
    public static function convertLessonsToJson(array $lessons)
    {
        $lessonsArray = array_map(function ($lesson) {
            return [
                'lesson_id' => $lesson->lessonId,
                'status' => $lesson->status,
            ];
        }, $lessons);

        return json_encode($lessonsArray);
    }

}