<?php

namespace console\controllers\copy;

use Yii;
use yii\console\Controller;

class CertificateCopyController extends Controller
{
    public function actionCopyCertificate(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM certificat");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('certificate',
                [
                    'id' => $record['id'],
                    'certificate_number' => $record['certificat_number'],
                    'certificate_template_id' => $record['certificat_template_id'],
                    'training_group_participant_id' => $record['training_group_participant_id'],
                    'status' => $record['status'],
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteCertificate()
    {
        Yii::$app->db->createCommand()->delete('certificate')->execute();
    }
    public function actionDeleteAll(){
        $this->actionDeleteCertificate();
    }
    public function actionCopyAll(){
        $this->actionCopyCertificate();
    }
}