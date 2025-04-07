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
    static public function getInfo($path)
    {
        /*$accessToken = Yii::$app->params['yandexApiKey'];
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://cloud-api.yandex.net/v1/disk/resources')
            ->setHeaders([
                'Authorization' => 'OAuth ' . $accessToken
            ])
            ->setData([
                'path' => $path
            ])
            ->send();
        $fileInfo = $response->data;
        return $fileInfo;
    }*/
        $token = Yii::$app->params['yandexApiKey'];
        $client = new Client();


        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://cloud-api.yandex.net/v1/disk/resources/download')
            ->addHeaders(['Authorization' => 'OAuth ' . $token])
            ->setData(['path' => $path])
            ->send();
        if (!$response->isOk) {
            throw new \Exception("Не удалось получить ссылку на скачивание: " . $response->content);
        }

        $downloadUrl = $response->data['href'];
        return $downloadUrl;
        $ch = curl_init($downloadUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // важно: следовать за редиректами
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // если нужны
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("Ошибка при скачивании файла. HTTP $httpCode");
        }
        // Отдаём файл
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="document.docx"');
        Yii::$app->response->headers->set('Content-Length', strlen($content));
        return $content;
    }
    static public function info($path)
    {
        $accessToken = Yii::$app->params['yandexApiKey'];
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://cloud-api.yandex.net/v1/disk/resources')
            ->setHeaders([
               'Authorization' => 'OAuth ' . $accessToken
            ])
            ->setData([
               'path' => $path
            ])->send();
        $fileInfo = $response->data;
        return $fileInfo;
    }
}