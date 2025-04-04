<?php

namespace console\controllers\seeders;
use frontend\models\work\team\ActParticipantBranchWork;
use frontend\models\work\team\ActParticipantWork;
use Yii;
use yii\console\Controller;

class ActParticipantBranchSeederController extends Controller
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
        while ($index < $amount){
            $actId = $this->randomHelper->randomItem(ActParticipantWork::find()->all())['id'];
            $branch = rand(1, 7);
            if (ActParticipantBranchWork::find()->andWhere(['act_participant_id' => $actId])->andWhere(['branch' => $branch])->count() == 0){
                $command = Yii::$app->db->createCommand();
                $command->insert('act_participant_branch', [
                    'act_participant_id' => $actId,
                    'branch' => $branch,
                ]);
                $command->execute();
                $index++;
            }
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('act_participant_branch')->execute();
    }
}