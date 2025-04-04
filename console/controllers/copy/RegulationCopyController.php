<?php

namespace console\controllers\copy;

use console\helper\FileTransferHelper;
use frontend\models\work\regulation\RegulationWork;
use Yii;
use yii\console\Controller;

class RegulationCopyController extends Controller
{
    public function actionCopyRegulation(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM regulation");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('regulation',
                [
                    'id' => $record['id'],
                    'date' => $record['date'],
                    'name' => $record['name'],
                    'order_id' => $record['order_id'],
                    'short_name' => $record['short_name'],
                    'ped_council_date' => $record['ped_council_date'],
                    'ped_council_number' => $record['ped_council_number'],
                    'par_council_date' => $record['par_council_date'],
                    'state' => $record['state'],
                    'regulation_type' => $record['regulation_type_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteRegulation(){
        Yii::$app->db->createCommand()->delete('regulation')->execute();
    }
    public function actionDeleteAll(){
        $this->actionDeleteRegulation();
    }
    public function actionCopyAll(){
        $this->actionCopyRegulation();
    }
}