<?php

namespace common\services\general\files;

use Arhitector\Yandex\Disk;
use Yii;

class YandexDiskContext
{
    static public function CheckSameFile($filepath)
    {
        $disk = new Disk(Yii::$app->params['yandexApiKey']);

        $resource = $disk->getResource('disk:/'.$filepath);

        return $resource->has();
    }

    static public function GetFileFromDisk($filepath)
    {
        $disk = new Disk(Yii::$app->params['yandexApiKey']);

        $resource = $disk->getResource($filepath);

        return $resource;

    }

    static public function UploadFileOnDisk($disk_filepath, $local_filepath,  $overwrite = false)
    {
        $disk = new Disk(Yii::$app->params['yandexApiKey']);

        $resource = $disk->getResource($disk_filepath);

        $resource->upload($local_filepath, $overwrite);
    }

    static public function DeleteFileFromDisk($filepath)
    {
        $disk = new Disk(Yii::$app->params['yandexApiKey']);

        $resource = $disk->getResource($filepath);

        return $resource->delete();
    }
}