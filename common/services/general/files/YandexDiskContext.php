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
        $accessToken = Yii::$app->params['yandexApiKey'];
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://cloud-api.yandex.net/v1/disk/resources/download')
            ->setHeaders([
               'Authorization' => 'OAuth ' . $accessToken
            ])
            ->setData([
               'path' => $path
            ])->send();

        $fileInfo = $response->data;
        return $fileInfo;
    }
    static public function download($url, $filename)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // получаем содержимое как строку
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // следовать редиректам

        $data = curl_exec($ch);

        if(curl_errno($ch)) {
            echo 'Ошибка curl: ' . curl_error($ch);
            exit;
        }

        curl_close($ch);

// Заголовки для отдачи файла
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($data));

// Отдаем файл
        echo $data;
        exit;
    }
    static public function downloadFileContent($url)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true, // <--- важно!
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; YourApp/1.0)',
            CURLOPT_TIMEOUT => 30,
        ]);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}