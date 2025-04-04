<?php

namespace common\components\files;

class CreateDirZip extends CreateZip
{
    public function getFilesFromFolder($directory, $put_into) {
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if (is_file($directory.$file)) {
                    $fileContents = file_get_contents($directory.$file);
                    $this->addFile($fileContents, $put_into.$file);
                } elseif ($file != '.' and $file != '..' and is_dir($directory.$file)) {
                    $this->addDirectory($put_into.$file.'/');
                    $this->getFilesFromFolder($directory.$file.'/', $put_into.$file.'/');
                }
            }
        }
        closedir($handle);
    }
}