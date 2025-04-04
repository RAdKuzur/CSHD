<?php

namespace console\controllers\copy;

use Yii;
use yii\console\Controller;

class ExpireCopyController extends Controller
{
    public function actionCopyExpire(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM expire");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('expire', [
                'id' => $record['id'],
                'active_regulation_id' => $record['active_regulation_id'],
                'expire_regulation_id' => $record['expire_regulation_id'],
                'expire_order_id' => $record['expire_order_id'],
                'document_type' => $record['document_type_id'],
                'expire_type' => $record['expire_type'],
            ]);
            $command->execute();
        }
    }
    public function actionDeleteExpire(){
        Yii::$app->db->createCommand()->delete('expire')->execute();
    }
    public function actionCopyAll(){
        $this->actionCopyExpire();
    }
    public function actionDeleteAll(){
        $this->actionDeleteExpire();
    }
}