<?php

namespace console\controllers\seeders;

use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\team\ActParticipantWork;
use Yii;
use yii\console\Controller;

class ParticipantAchievementSeederController extends Controller
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
        $index = 0;
        while($index < $amount){
            $actId = $this->randomHelper->randomItem(ActParticipantWork::find()->all())['id'];
            if (ParticipantAchievementWork::find()->where(['act_participant_id' => $actId])->count() == 0){
                $command = Yii::$app->db->createCommand();
                $command->insert('participant_achievement', [
                    'act_participant_id' => $actId,
                    'achievement' => $this->randomHelper->generateRandomString(),
                    'type' => rand(1, 4),
                    'cert_number' => $this->randomHelper->generateRandomString(),
                    'nomination' => $this->randomHelper->generateRandomString(),
                    'date' => $this->randomHelper->randomDate()
                ]);
                $command->execute();
                $index++;
            }
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('participant_achievement')->execute();
    }
}