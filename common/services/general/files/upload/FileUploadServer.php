<?php

namespace common\services\general\files\upload;

use Yii;


class FileUploadServer extends AbstractFileUpload
{
    public $ADDITIONAL_PATH = ''; //дополнительный путь к папке на сервере

    function __construct($tFilepath, $tFilename)
    {
        $this->filepath = $tFilepath;
        $this->filename = $tFilename;
        $this->ADDITIONAL_PATH = Yii::$app->basePath;
    }

    public function LoadFile()
    {
        $file = $this->ADDITIONAL_PATH.$this->filepath.$this->filename;

        if (file_exists($file)) {
            $this->success = true;
            $this->file = $file;
            return $this->success;
        }

        $this->success = false;
        return $this->success;
    }
}