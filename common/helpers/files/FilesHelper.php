<?php

namespace common\helpers\files;

use common\helpers\StringFormatter;
use common\repositories\general\FilesRepository;
use frontend\models\work\general\FilesWork;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Url;

class FilesHelper
{
    const _MAX_FILE_SIZE = 15728640; // максимальный размер файла для размещения на сервере
    const TYPE_LINK = 'link'; // особый тип, прямая ссылка на хранилище

    const TYPE_SCAN = 'scan';
    const TYPE_DOC = 'doc';
    const TYPE_APP = 'app';
    const TYPE_PROTOCOL = 'protocol';
    const TYPE_PHOTO = 'photo';
    const TYPE_REPORT = 'report';
    const TYPE_OTHER = 'other';
    const TYPE_MAIN = 'main';
    const TYPE_CONTRACT = 'contract';
    const TYPE_PRESENTATION = 'presentation';
    const TYPE_WORK = 'work';
    const TYPE_PARTICIPATION = 'participation';
    const TYPE_MATERIAL = 'material';

    const FILE_SERVER = 'server';
    const FILE_YADI = 'yadi';

    const LOAD_TYPE_SINGLE = 'single'; /** тип загрузки "единичный". перезаписывает существующую строку в БД при наличии @see FilesWork */
    const LOAD_TYPE_MULTI = 'multi'; /** тип загрузки "мульти". создает новые записи в @see FilesWork вне зависимости от существующих аналогичных строк*/

    public static function getFileTypes()
    {
        return [
            self::TYPE_LINK => 'Ссылка',
            self::TYPE_SCAN => 'Сканы документов',
            self::TYPE_DOC => 'Редактируемые файлы документов',
            self::TYPE_APP => 'Приложения к документам',
            self::TYPE_PROTOCOL => 'Протоколы',
            self::TYPE_PHOTO => 'Фотоматериалы',
            self::TYPE_REPORT => 'Явочные документы',
            self::TYPE_OTHER => 'Другие файлы',
            self::TYPE_MAIN => 'Основные документы',
            self::TYPE_CONTRACT => 'Файлы договоров',
            self::TYPE_PRESENTATION => 'Презентационные материалы',
            self::TYPE_MATERIAL => 'Представленные материалы',
            self::TYPE_WORK => 'Рабочие материалы',
        ];
    }

    public static function getFileType($index)
    {
        return self::getFileTypes()[$index];
    }

    public static function getFilenameFromPath($filepath)
    {
        $parts = explode('/', $filepath);
        return end($parts);
    }

    /**
     * Создает относительный путь к файлу на основе
     * @param string $tableName имени таблицы
     * @param string $fileType типа файла
     * @return string
     */
    public static function createAdditionalPath(string $tableName, string $fileType)
    {
        if ($fileType == FilesHelper::TYPE_LINK) {
            return ''; // если тип ссылочный, то не требуется дополнительного пути к filepath
        }

        return FilePaths::BASE_FILEPATH . '/' . $tableName . '/' . $fileType . '/';
    }

    public static function createFileLinks($object, $filetype, $addPath)
    {
        $files = (Yii::createObject(FilesRepository::class))->get($object::tableName(), $object->id, $filetype);
        $links = [];
        if (count($files) > 0) {
            foreach ($files as $file) {
                /** @var FilesWork $file */
                $links[] = [
                    'link' => StringFormatter::stringAsLink(
                        FilesHelper::getFilenameFromPath($file->filepath),
                        Url::to(['get-file', 'filepath' => $addPath . $file->filepath])
                    ),
                    'id' => $file->id
                ];
            }
        }

        return $links;
    }

    public static function createFilePaths($object, $filetype, $addPath)
    {
        $files = (Yii::createObject(FilesRepository::class))->get($object::tableName(), $object->id, $filetype);
        $links = [];
        if (count($files) > 0) {
            foreach ($files as $file) {
                /** @var FilesWork $file */
                $links[] = $addPath . $file->filepath;
            }
        }

        return $links;
    }

    public static function createDirectory(string $path)
    {
        FileHelper::createDirectory($path);
    }

    public static function removeDirectory(string $path)
    {
        FileHelper::removeDirectory($path);
    }
}