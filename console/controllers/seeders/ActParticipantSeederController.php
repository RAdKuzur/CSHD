<?php

namespace console\controllers\seeders;

use common\models\scaffold\TeamName;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\general\PeopleStampWork;
use Yii;
use yii\console\Controller;

class ActParticipantSeederController extends Controller
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
        for($i = 0; $i < $amount * 2; $i++) {
            $command = Yii::$app->db->createCommand();
            $command->insert('team_name', [
                'name' => $this->randomHelper->generateRandomString(5)
            ]);
            $command->execute();
        }
        for($i = 0; $i < $amount; $i++){
            $command = Yii::$app->db->createCommand();
            $command->insert('act_participant', [
                'teacher_id' => $this->randomHelper->randomItem(PeopleStampWork::find()->all())['id'],
                'teacher2_id' => $this->randomHelper->randomItem(PeopleStampWork::find()->all())['id'],
                'focus'	=> rand(1,5),
                'type' => rand(1,2),
                'nomination' => $this->randomHelper->generateRandomString(15),
                'team_name_id' => $this->randomHelper->randomItem(TeamName::find()->all())['id'],
                'form' => rand(1,3),
                'foreign_event_id' => $this->randomHelper->randomItem(ForeignEventWork::find()->all())['id'],
                'allow_remote' => rand(1,3)
            ]);
            $command->execute();
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('act_participant')->execute();
        Yii::$app->db->createCommand()->delete('team_name')->execute();
    }
}