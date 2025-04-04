<?php

namespace console\controllers\seeders;

use common\models\work\UserWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\general\PeopleStampWork;
use Yii;
use yii\console\Controller;

class ThematicPlanSeederController extends Controller
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
            $command->insert('thematic_plan', [
                'theme' => $this->randomHelper->generateRandomString(15),
                'training_program_id' => $this->randomHelper->randomItem(TrainingProgramWork::find()->all())['id'],
                'control_type' => rand(1, 5),
            ]);
            $command->execute();
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('thematic_plan')->execute();
    }
}