<?php

namespace common\helpers\files\filenames;

use frontend\models\work\order\OrderMainWork;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\repositories\general\FilesRepository;
use DomainException;
use frontend\forms\training_group\TrainingGroupBaseForm;
use frontend\models\work\general\FilesWork;
use InvalidArgumentException;

class TrainingGroupFileNameGenerator implements FileNameGeneratorInterface
{
    private FilesRepository $filesRepository;

    public function __construct(FilesRepository $filesRepository)
    {
        $this->filesRepository = $filesRepository;
    }
    public function getOrdinalFileNumber($object, $fileType)
    {
        switch ($fileType) {
            case FilesHelper::TYPE_PHOTO:
                return $this->getOrdinalFileNumberPhoto($object);
            case FilesHelper::TYPE_PRESENTATION:
                return $this->getOrdinalFileNumberPresentation($object);
            case FilesHelper::TYPE_WORK:
                return $this->getOrdinalFileNumberWork($object);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }
    private function getOrdinalFileNumberPhoto($object)
    {
        $lastDocFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_PHOTO);
        /** @var FilesWork $lastDocFile */
        if ($lastDocFile) {
            preg_match('/Фото(\d+)_/', basename($lastDocFile->filepath), $matches);
            return (int)$matches[1];
        }

        return 0;
    }

    private function getOrdinalFileNumberPresentation($object)
    {
        $lastAppFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_PRESENTATION);
        /** @var FilesWork $lastAppFile */
        if ($lastAppFile) {
            preg_match('/През(\d+)_/', basename($lastAppFile->filepath), $matches);
            return (int)$matches[1];
        }

        return 0;
    }

    private function getOrdinalFileNumberWork($object)
    {
        $lastAppFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_WORK);
        /** @var FilesWork $lastAppFile */
        if ($lastAppFile) {
            preg_match('/Раб(\d+)_/', basename($lastAppFile->filepath), $matches);
            return (int)$matches[1];
        }

        return 0;
    }

    public function generateFileName($object, $fileType, $params = []): string
    {
        switch ($fileType) {
            case FilesHelper::TYPE_PHOTO:
                return $this->generatePhotoFileName($object, $params);
            case FilesHelper::TYPE_PRESENTATION:
                return $this->generatePresentationFileName($object, $params);
            case FilesHelper::TYPE_WORK:
                return $this->generateWorkFileName($object, $params);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }
    private function generatePhotoFileName($object, $params = [])
    {
        /** @var TrainingGroupBaseForm $object */
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        $new_date = DateFormatter::format($object->startDate, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename = 'Фото'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_PHOTO) + $params['counter']).'_'.$new_date.'_'.$object->id;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);
        return $res . '.' . $object->photos[$params['counter'] - 1]->extension;
    }

    private function generatePresentationFileName($object, $params = [])
    {
        /** @var TrainingGroupBaseForm $object */
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        $new_date = DateFormatter::format($object->startDate, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename = 'През'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_PRESENTATION) + $params['counter']).'_'.$new_date.'_'.$object->id;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);
        return $res . '.' . $object->presentations[$params['counter'] - 1]->extension;
    }

    private function generateWorkFileName($object, $params = [])
    {
        /** @var TrainingGroupBaseForm $object */
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        $new_date = DateFormatter::format($object->startDate, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename = 'Раб'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_WORK) + $params['counter']).'_'.$new_date.'_'.$object->id;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);
        return $res . '.' . $object->workMaterials[$params['counter'] - 1]->extension;
    }







}