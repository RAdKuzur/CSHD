<?php


namespace common\repositories\educational;


use common\components\traits\CommonDatabaseFunctions;
use DomainException;
use frontend\models\work\CertificateTemplatesWork;
use frontend\models\work\educational\CertificateWork;
use Yii;

class CertificateRepository
{
    public function get($id)
    {
        return CertificateWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return CertificateWork::find()->all();
    }

    public function getCount()
    {
        return CertificateWork::find()->count();
    }

    public function getByGroupParticipantId(int $groupParticipantId)
    {
        return CertificateWork::find()
            ->where(['training_group_participant_id' => $groupParticipantId])
            ->one();
    }

    public function getCertificatesByGroupId(int $groupId)
    {
        return CertificateWork::find()->
            joinWith(['trainingGroupParticipantWork'])
            ->where(['training_group_participant.training_group_id' => $groupId])
            ->all();
    }

    public function prepareSetStatus($id, $status)
    {
        $command = Yii::$app->db->createCommand();
        $command->update('certificate', ['status' => $status], "id = $id");
        return $command->getRawSql();
    }

    public function save(CertificateWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения темы проекта. Проблемы: '.json_encode($model->getErrors()));
        }

        return $model->id;
    }
}