<?php

namespace console\controllers\copy;
use Yii;
use yii\console\Controller;

class PersonalDataCopyController extends Controller
{
    public function actionCopyPersonalDataParticipant(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM personal_data_foreign_event_participant");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('personal_data_participant',
                [
                    'id' => $record['id'],
                    'participant_id' => $record['foreign_event_participant_id'],
                    'personal_data' => $record['personal_data_id'],
                    'status' => $record['status'],
                ]
            );
            $command->execute();
        }
    }
    public function actionDeletePersonalData(){
        Yii::$app->db->createCommand()->delete('personal_data_participant')->execute();
    }
    public function actionDeleteAll(){
        $this->actionDeletePersonalData();
    }
    public function actionCopyAll(){
        $this->actionCopyPersonalDataParticipant();
    }
}