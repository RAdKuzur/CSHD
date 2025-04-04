<?php

namespace console\controllers\seeders;

use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\team\ActParticipantWork;
use frontend\models\work\team\SquadParticipantWork;
use Yii;
use yii\console\Controller;

class SquadParticipantSeederController extends Controller
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
        ini_set('memory_limit', '1024M');
        $index = 0;
        while ($index < $amount){
            $actId = $this->randomHelper->randomItem(ActParticipantWork::find()->all())['id'];
            $participantId = $this->randomHelper->randomItem(ForeignEventParticipantsWork::find()->all())['id'];
            if (SquadParticipantWork::find()->andWhere(['act_participant_id' => $actId])->andWhere(['participant_id' => $participantId])->count() == 0) {
                $command = Yii::$app->db->createCommand();
                $command->insert('squad_participant', [
                    'act_participant_id' => $actId,
                    'participant_id' => $participantId,
                ]);
                $command->execute();
                $index++;
            }
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('squad_participant')->execute();
    }
}