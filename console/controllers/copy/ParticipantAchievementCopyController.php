<?php

namespace console\controllers\copy;

use Yii;
use yii\console\Controller;

class ParticipantAchievementCopyController extends Controller
{
    public function actionCopyParticipantAchievement($id = null, $modelActId = null){
        $command = Yii::$app->db->createCommand();
        $record = Yii::$app->old_db->createCommand("SELECT * FROM participant_achievement WHERE teacher_participant_id = $id")->queryAll();
        if (count($record) > 0 && count($record) < 2) {
            $command->insert('participant_achievement', [
                'act_participant_id' => $modelActId,
                'achievement' => $record[0]['achievment'],
                'type' => $record[0]['winner'],
                'cert_number' => $record[0]['cert_number'],
                'nomination' => $record[0]['nomination'],
                'date' => $record[0]['date'],
            ]);
            $command->execute();
        }
        else if (count($record) > 1) {
            foreach ($record as $item) {
                $command->insert('participant_achievement', [
                    'act_participant_id' => $modelActId,
                    'achievement' => $item['achievment'],
                    'type' => $item['winner'],
                    'cert_number' => $item['cert_number'],
                    'nomination' =>$item['nomination'],
                    'date' => $item['date'],
                ]);
                $command->execute();
            }
        }
    }
    public function actionDeleteParticipantAchievement(){
        Yii::$app->db->createCommand()->delete('participant_achievement')->execute();
    }
    public function actionDeleteAll(){
        $this->actionDeleteParticipantAchievement();
    }
    public function actionCopyAll(){
        $this->actionCopyParticipantAchievement();
    }
}