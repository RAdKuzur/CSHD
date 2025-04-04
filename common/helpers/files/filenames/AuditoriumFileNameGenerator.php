<?php

namespace common\helpers\files\filenames;

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\repositories\general\FilesRepository;
use DomainException;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\general\FilesWork;
use frontend\models\work\regulation\RegulationWork;
use InvalidArgumentException;

class AuditoriumFileNameGenerator implements FileNameGeneratorInterface
{
    private FilesRepository $filesRepository;

    public function __construct(FilesRepository $filesRepository)
    {
        $this->filesRepository = $filesRepository;
    }

    public function generateFileName($object, $fileType, $params = []): string
    {
        switch ($fileType) {
            case FilesHelper::TYPE_OTHER:
                return $this->generateOtherFileName($object, $params);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }

    public function getOrdinalFileNumber($object, $fileType)
    {
        switch ($fileType) {
            case FilesHelper::TYPE_OTHER:
                return $this->getOrdinalFileNumberOther($object);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }

    private function getOrdinalFileNumberOther($object)
    {
        $lastOtherFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_OTHER);
        /** @var FilesWork $lastOtherFile */
        if ($lastOtherFile) {
            preg_match('/Файл(\d+)_/', basename($lastOtherFile->filepath), $matches);
            return (int)$matches[1];
        }

        return 0;
    }

    private function generateOtherFileName($object, $params)
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        /** @var AuditoriumWork $object */
        $filename =
            'Файл'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_OTHER) + $params['counter']).
            '_'.$object->name.'_'.$object->id;

        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->filesList[$params['counter'] - 1]->extension;
    }
}