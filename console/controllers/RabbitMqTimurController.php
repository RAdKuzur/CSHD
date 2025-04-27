<?php

namespace console\controllers;


use yii\console\Controller;
use yii\helpers\Console;
use Yii;
class RabbitMqTimurController extends \yii\console\Controller
{
    /**
     * Отправка файла в очередь
     * @param string $filePath Путь к файлу
     */
    public function actionSend($filePath)
{
    if (!file_exists($filePath)) {
        $this->stderr("Файл не найден: {$filePath}\n", Console::FG_RED);
        return 1;
    }

    $fileContent = file_get_contents($filePath);
    $fileName = basename($filePath);

    $message = [
        'filename' => $fileName,
        'content' => base64_encode($fileContent),
        'timestamp' => time(),
    ];

    Yii::$app->queue->send(json_encode($message));

    $this->stdout("Файл {$fileName} отправлен в очередь\n", Console::FG_GREEN);
    return 0;
}

    /**
     * Получение файла из очереди
     */
    public function actionConsume()
{
    $this->stdout("Ожидание файлов...\n", Console::FG_YELLOW);

    Yii::$app->queue->listen(function($message) {
        $data = json_decode($message, true);

        $fileName = $data['filename'];
        $fileContent = base64_decode($data['content']);
        $timestamp = $data['timestamp'];

        $savePath = Yii::getAlias('@runtime/queue_files/') . $fileName;

        if (!is_dir(dirname($savePath))) {
            mkdir(dirname($savePath), 0777, true);
        }

        file_put_contents($savePath, $fileContent);

        $this->stdout("Получен файл: {$fileName}\n", Console::FG_GREEN);
        $this->stdout("Сохранен в: {$savePath}\n");
        $this->stdout("Размер: " . filesize($savePath) . " байт\n");
        $this->stdout("Отправлен: " . date('Y-m-d H:i:s', $timestamp) . "\n\n");

        return true; // Подтверждаем обработку
    });
}
}