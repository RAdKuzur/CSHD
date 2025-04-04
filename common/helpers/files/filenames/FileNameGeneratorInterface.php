<?php

namespace common\helpers\files\filenames;

use Matrix\Exception;

interface FileNameGeneratorInterface
{
    /**
     * Основная функция генерации имени файла
     * @param $object
     * @param $fileType
     * @param $params
     * @return string
     */
    public function generateFileName($object, $fileType, $params = []): string;

    /**
     * Возвращает последний порядковый номер файла (если допускается мультизагрузка файлов)
     * @param $object
     * @param $fileType
     * @return mixed
     */
    public function getOrdinalFileNumber($object, $fileType);
}