<?php


namespace common\repositories\educational;


use common\components\traits\CommonDatabaseFunctions;
use DomainException;
use frontend\models\work\CertificateTemplatesWork;

class CertificateTemplatesRepository
{
    use CommonDatabaseFunctions;

    public function get($id)
    {
        return CertificateTemplatesWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return CertificateTemplatesWork::find()->all();
    }

    public function delete(CertificateTemplatesWork $model)
    {
        return $model->delete();
    }

    public function save(CertificateTemplatesWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения темы проекта. Проблемы: '.json_encode($model->getErrors()));
        }

        return $model->id;
    }
}