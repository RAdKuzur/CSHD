<?php

namespace console\controllers\seeders;

use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\console\Controller;

class TrainingGroupLessonSeederController extends Controller
{
    private RandomHelper $randomHelper;
    public function __construct(
        $id,
        $module,
        RandomHelper $randomHelper,
        $config = []
    )
    {
        $this->randomHelper = $randomHelper;
        parent::__construct($id, $module, $config);
    }
    public function actionRun($amount = 50){
        for($i = 0; $i < $amount; $i++){
            $command = Yii::$app->db->createCommand();
            $startTime = $this->randomHelper->randomTime();
            $endTime = $this->randomHelper->randomTime($startTime);
            $command->insert('training_group_lesson', [
                'lesson_date' => $this->randomHelper->randomDate(),
                'lesson_start_time' => $startTime,
                'lesson_end_time' => $endTime,
                'duration' => rand(1, 60),
                'branch' => rand(1, 7),
                'auditorium_id' => $this->randomHelper->randomItem(AuditoriumWork::find()->all())['id'],
                'training_group_id' =>  $this->randomHelper->randomItem(TrainingGroupWork::find()->all())['id'],
            ]);
            $command->execute();
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('training_group_lesson')->execute();
    }
}