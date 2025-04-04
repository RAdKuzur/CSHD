<?php

namespace frontend\services\event;

use frontend\forms\OrderEventForm;
use yii\web\UploadedFile;

class OrderEventFormService
{
    public function getFilesInstances(OrderEventForm $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
        $model->actFiles = UploadedFile::getInstances($model, 'actFiles');
    }
}