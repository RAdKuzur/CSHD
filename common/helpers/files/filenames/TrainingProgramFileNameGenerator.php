<?php

namespace common\helpers\files\filenames;

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\repositories\general\FilesRepository;
use DomainException;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\general\FilesWork;
use InvalidArgumentException;

class TrainingProgramFileNameGenerator implements FileNameGeneratorInterface
{
    private FilesRepository $filesRepository;

    public function __construct(FilesRepository $filesRepository)
    {
        $this->filesRepository = $filesRepository;
    }

    public function generateFileName($object, $fileType, $params = []): string
    {
        switch ($fileType) {
            case FilesHelper::TYPE_MAIN:
                return $this->generateMainFileName($object, $params);
            case FilesHelper::TYPE_DOC:
                return $this->generateDocFileName($object, $params);
            case FilesHelper::TYPE_CONTRACT:
                return $this->generateContractFileName($object, $params);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }

    public function getOrdinalFileNumber($object, $fileType)
    {
        switch ($fileType) {
            case FilesHelper::TYPE_DOC:
                return $this->getOrdinalFileNumberDoc($object);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }

    private function getOrdinalFileNumberDoc($object)
    {
        $lastDocFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_DOC);
        /** @var FilesWork $lastDocFile */
        if ($lastDocFile) {
            preg_match('/Ред(\d+)_/', basename($lastDocFile->filepath), $matches);
            return (int)$matches[1];
        }

        return 0;
    }

    private function generateDocFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        /** @var TrainingProgramWork $object */
        $date = $object->ped_council_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename = 'Ред'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_DOC) + $params['counter']).'_'.$new_date.'_'.$object->name;

        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->docFiles[$params['counter'] - 1]->extension;
    }

    private function generateMainFileName($object, $params = [])
    {
        /** @var TrainingProgramWork $object */
        $date = $object->ped_council_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename = 'Док.'.$new_date.'_'.$object->name;

        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->mainFile->extension;
    }

    private function generateContractFileName($object, $params = [])
    {
        /** @var TrainingProgramWork $object */
        $date = $object->ped_council_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename = 'Дог.'.$new_date.'_'.$object->name;

        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->contractFile->extension;
    }
}