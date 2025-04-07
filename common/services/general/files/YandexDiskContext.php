<?php

namespace common\services\general\files;

use Arhitector\Yandex\Disk;
use Yii;
use yii\base\Component;
use yii\httpclient\Client;
use yii\web\Response;

class YandexDiskContext
{
    public const BASE_FOLDER = 'DSSD';
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
    static public function info($path)
    {
        $disk = new Disk(Yii::$app->params['yandexApiKey']);
    }
}