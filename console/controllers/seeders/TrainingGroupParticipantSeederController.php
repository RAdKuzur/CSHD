<?php

namespace console\controllers\seeders;

use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\console\Controller;

class TrainingGroupParticipantSeederController extends Controller
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
            $command->insert('training_group_participant', [
                'participant_id' => $this->randomHelper->randomItem(ForeignEventParticipantsWork::find()->all())['id'],
                'certificat_number' => $this->randomHelper->generateRandomString(),
                'send_method' => rand(0, 7),
                'training_group_id' => $this->randomHelper->randomItem(TrainingGroupWork::find()->all())['id'],
                'status' => 1,
                'success' => 0,
                'points' => 0,
                'group_project_themes_id' => NULL // $this->randomHelper->randomItem(GroupProjectThemesWork::find()->all())['id'],
            ]);
            $command->execute();
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('training_group_participant')->execute();
    }
}