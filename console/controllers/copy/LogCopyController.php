<?php

namespace console\controllers\copy;

use Yii;
use yii\console\Controller;

class LogCopyController extends Controller
{
    public function actionCopyLog()
    {
        $query = Yii::$app->old_db->createCommand("SELECT * FROM log");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('log',
                [
                    'id' => $record['id'],
                    'datetime' => $record['date'] . ' ' . $record['time'],
                    'level' => NULL,
                    'type' => NULL,
                    'user_id' => $record['user_id'],
                    'text' => $record['text'],
                    'add_data' => NULL,
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteLogCopy()
    {
        Yii::$app->db->createCommand()->delete('log')->execute();
    }
    public function actionDeleteAll()
    {
        $this->actionDeleteLogCopy();
    }
    public function actionCopyAll(){
        $this->actionCopyLog();
    }
}