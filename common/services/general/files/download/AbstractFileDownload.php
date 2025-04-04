<?php


namespace common\services\general\files\download;

abstract class AbstractFileDownload
{
    public $filepath;

    public $success;
    public $file;

    abstract public function LoadFile();
}