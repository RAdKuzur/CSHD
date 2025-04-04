<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use DateTime;
use frontend\events\educational\training_group\CreateLessonGroupEvent;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use Yii;
use yii\base\Model;

class TrainingGroupScheduleForm extends Model
{
    use EventTrait;

    const MANUAL = 0;
    const AUTO = 1;

    /** @var TrainingGroupWork  */
    public $trainingGroup;

    /** @var TrainingProgramWork  */
    public $trainingProgram;

    public $id;
    public $number;
    public $type;
    public $lessons;
    public $prevLessons;

    public function __construct($id = -1, $config = [])
    {
        parent::__construct($config);
        if ($id !== -1) {
            $this->lessons = (Yii::createObject(TrainingGroupRepository::class))->getLessons($id);
            $this->prevLessons = (Yii::createObject(TrainingGroupRepository::class))->getLessons($id);
            foreach ($this->prevLessons as $lesson) {
                /** @var TrainingGroupLessonWork $lesson */
                $lesson->setAuditoriumName();
            }

            $this->trainingGroup = (Yii::createObject(TrainingGroupRepository::class))->get($id);
            $this->number = $this->trainingGroup->number;
            $this->trainingProgram = (Yii::createObject(TrainingProgramRepository::class))->get($this->trainingGroup->training_program_id);
            $this->id = $id;
        }
        else {
            $this->prevLessons = [];
        }
    }

    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['lessons', 'prevLessons', 'id', 'number', 'trainingProgram'], 'safe']
        ];
    }

    public function isManual()
    {
        return $this->type == self::MANUAL;
    }

    /**
     * Функция преобразования периодов ("каждый понедельник", "каждый вторник") в конкретные даты занятий
     * @return void
     */
    public function convertPeriodToLessons()
    {
        $newLessons = [];

        // Проходим по всем записям динамической формы
        foreach ($this->lessons as $lesson) {
            /** @var TrainingGroupLessonWork $lesson */
            $days = $lesson->autoDate;
            $startDates = [];
            $currentDate = new DateTime($this->trainingGroup->start_date);
            $finishDate = new DateTime($this->trainingGroup->finish_date);

            // Поиск стартовых дат для авторасчета
            while ($currentDate <= $finishDate && count($days) > 0) {
                foreach ($days as $key => $day) {
                    if ($currentDate->format('N') == $day) {
                        $startDates[$key] = clone $currentDate;
                        unset($days[$key]);
                        break;
                    }
                }
                $currentDate->modify('+1 day');
            }

            // Расчет окончательных дат занятий
            $finalDates = [];
            foreach ($startDates as $index => $startDate) {
                $currentDate = clone $startDate;
                while ($currentDate <= $finishDate) {
                    $finalDates[] = clone $currentDate;
                    $currentDate->modify('+7 days');
                }
            }

            // Заполнение массива занятий для текущей формы
            foreach ($finalDates as $date) {
                $newLessons[] = TrainingGroupLessonWork::fill(
                    $this->id,
                    $date->format('Y-m-d'),
                    $lesson->lesson_start_time,
                    $lesson->branch,
                    $lesson->auditorium_id,
                    $lesson->lesson_end_time,
                    $lesson->duration,
                );
            }
        }

        $this->lessons = $newLessons;
    }
}