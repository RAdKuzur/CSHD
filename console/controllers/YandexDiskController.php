<?php

namespace console\controllers;

use common\components\RabbitMQ;
use common\helpers\common\QueryHelper;
use common\services\general\files\FileService;
use common\services\general\files\YandexDiskContext;
use Yii;
use yii\console\Controller;

class YandexDiskController extends Controller
{
    const LOCAL_FILE_PATH = 'C:\Users\Praktik\Downloads\visit.sql';
    const YANDEX_FILE_PATH = 'CSHD';
    private FileService $fileService;
    public function __construct(
        $id,
        $module,
        FileService $fileService,
        $config = []
    )
    {
        $this->fileService = $fileService;
        parent::__construct($id, $module, $config);
    }

    public function actionSendMessage()
    {
        if (file_exists(self::LOCAL_FILE_PATH)) {
            $taskData = json_encode([
                'localPath' => self::LOCAL_FILE_PATH,
                'yandexPath' => self::YANDEX_FILE_PATH . '/test'
            ]);
            Yii::$app->rabbitmq->publish('file_upload_queue', $taskData);
            echo "Задача на загрузку отправлена в очередь.\n";
        } else {
            echo "Файл не найден по указанному пути.\n";
        }
    }
    public function actionConsumeMessage()
    {
        Yii::$app->rabbitmq->consume('file_upload_queue', function ($message) {
            $task = json_decode($message, true);
            if (!$task || !isset($task['localPath'], $task['yandexPath'])) {
                echo "Неверный формат задания.\n";
                return;
            }

            if (file_exists($task['localPath'])) {
                echo "Загрузка файла на Яндекс.Диск: " . $task['yandexPath'] . "\n";
                YandexDiskContext::UploadFileOnDisk($task['yandexPath'], $task['localPath'], true);
                echo "Файл успешно загружен.\n";
                echo "Попытка удалить файл ". $task['localPath'] ."\n";
                if (YandexDiskContext::CheckSameFile($task['yandexPath'])){
                    unlink($task['localPath']);
                    echo "Файл ". $task['localPath'] ." удалён \n";
                }
            } else {
                echo "Файл не найден: " . $task['localPath'] . "\n";
            }
        }, true);
    }

    public function actionTestSend(){
        Yii::$app->rabbitmq->publish('my_queue', json_encode(['task' => 'send_email', 'email' => 'user@example.com']));
    }

    public function actionTestConsume(){
        Yii::$app->rabbitmq->consume('my_queue', function ($message) {
            echo "Received: " . $message . "\n";
        });
    }
}