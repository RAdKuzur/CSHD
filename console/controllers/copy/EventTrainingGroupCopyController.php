<?php

namespace console\controllers\copy;

use Yii;
use yii\console\Controller;

class EventTrainingGroupCopyController extends Controller
{
    public function actionCopyEventTrainingGroup(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM event_training_group");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('event_training_group', $record);
            $command->execute();
        }
    }
    public function actionDeleteEventTrainingGroupCopy()
    {
        Yii::$app->db->createCommand()->delete('event_training_group')->execute();
    }
    public function actionDeleteAll(){
        $this->actionDeleteEventTrainingGroupCopy();
    }
    public function actionCopyAll(){
        $this->actionCopyEventTrainingGroup();
    }
}