<?php


namespace common\services\general\files\download;

use Yii;

class FileDownloadServer extends AbstractFileDownload
{
    public $ADDITIONAL_PATH = ''; //дополнительный путь к папке на сервере

    function __construct($tFilepath)
    {
        $this->filepath = $tFilepath;
        $this->ADDITIONAL_PATH = Yii::$app->basePath;
    }

    public function LoadFile()
    {
        $file = $this->ADDITIONAL_PATH . $this->filepath;

        if (file_exists($file)) {
            $this->success = true;
            $this->file = $file;
            return $this->success;
        }

        $this->success = false;
        return $this->success;
    }
}