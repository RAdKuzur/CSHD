<?php


namespace common\services\general\files\upload;


abstract class AbstractFileUpload
{
    public $filename;
    public $filepath;

    public $success;
    //public $file;

    abstract public function LoadFile();
}