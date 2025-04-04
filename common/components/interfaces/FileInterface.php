<?php

namespace common\components\interfaces;

use frontend\models\work\general\FilesWork;

interface FileInterface
{
    /**
     * Возвращает массив ссылок на скачивание файлов и ID записей в таблице @see FilesWork
     * @param $filetype
     * @return array
     */
    public function getFileLinks($filetype) : array;

    /**
     * Возвращает массив путей к файлам
     * @param $filetype
     * @return array
     */
    public function getFilePaths($filetype) : array;
}