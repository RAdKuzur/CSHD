<?php

namespace frontend\models\mock;

use frontend\models\work\educational\training_group\TrainingGroupLessonWork;

class TrainingGroupLessonMock extends TrainingGroupLessonWork
{
    public int $id;
    public int $training_group_id;
    public string $lesson_date;
    public string $lesson_start_time;
    public int $branch;
    public int $auditorium_id;
    public string $lesson_end_time;
    public int $duration;

    public function __construct(
        int $id = 0,
        int $trainingGroupId = 0,
        string $lessonDate = '',
        string $lessonStartTime = '',
        int $branch = 0,
        int $auditoriumId = 0,
        string $lessonEndTime = '',
        int $duration = 0,
        $config = []
    )
    {
        parent::__construct($config);
        $this->id = $id;
        $this->training_group_id = $trainingGroupId;
        $this->lesson_date = $lessonDate;
        $this->lesson_start_time = $lessonStartTime;
        $this->branch = $branch;
        $this->auditorium_id = $auditoriumId;
        $this->lesson_end_time = $lessonEndTime;
        $this->duration = $duration;
    }
}