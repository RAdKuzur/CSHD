<?php

namespace console\controllers\seeders;


use common\models\work\UserWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use Yii;
use yii\console\Controller;

class TrainingGroupSeederController extends Controller
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
    public function actionRun($amount = 10){
        for($i = 0; $i < $amount; $i++){
            $command = Yii::$app->db->createCommand();
            $startDate = $this->randomHelper->randomDate();
            $endDate = $this->randomHelper->randomDate($startDate);
            $protectionDate = $this->randomHelper->randomDate($startDate, $endDate);
            $command->insert('training_group', [
                'number' => $this->randomHelper->generateRandomString(15),
                'training_program_id' => $this->randomHelper->randomItem(TrainingProgramWork::find()->all())['id'],
                'start_date' => $startDate,
                'finish_date' => $endDate,
                'open' => rand(0, 1),
                'budget' => rand(0, 1),
                'branch' => rand(1, 7),
                'order_stop' => rand(0, 1),
                'archive' => 0,
                'protection_date' => $protectionDate,
                'protection_confirm' => NULL,
                'is_network' => NULL,
                'state' => rand(0, 1),
                'creator_id' => $this->randomHelper->randomItem(UserWork::find()->all())['id'],
                'last_edit_id' => $this->randomHelper->randomItem(UserWork::find()->all())['id'],
            ]);
            $command->execute();
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('training_group')->execute();
    }
}