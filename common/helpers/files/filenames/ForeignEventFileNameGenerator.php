<?php

namespace common\helpers\files\filenames;

use frontend\models\work\event\ForeignEventWork;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\repositories\general\FilesRepository;
use DomainException;
use frontend\forms\event\EventParticipantForm;
use frontend\forms\event\ForeignEventForm;
use frontend\models\work\general\FilesWork;
use InvalidArgumentException;

class ForeignEventFileNameGenerator implements FileNameGeneratorInterface
{
    private FilesRepository $filesRepository;

    public function __construct(FilesRepository $filesRepository)
    {
        $this->filesRepository = $filesRepository;
    }
    public function getOrdinalFileNumber($object, $fileType)
    {
        switch ($fileType) {
            case FilesHelper::TYPE_DOC:
                return $this->getOrdinalFileNumberDoc($object);
            case FilesHelper::TYPE_APP:
                return $this->getOrdinalFileNumberApp($object);
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

    private function getOrdinalFileNumberApp($object)
    {
        $lastAppFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_APP);
        /** @var FilesWork $lastAppFile */
        if ($lastAppFile) {
            preg_match('/М.(\d+)_/', basename($lastAppFile->filepath), $matches);
            return (int)$matches[1];
        }

        return 0;
    }

    public function generateFileName($object, $fileType, $params = []): string
    {
        switch ($fileType) {
            case FilesHelper::TYPE_PARTICIPATION:
                return $this->generateActFileName($object, $params);
            case FilesHelper::TYPE_DOC:
                return $this->generateAchievementFileName($object, $params);
            case FilesHelper::TYPE_MATERIAL:
                return $this->generateMaterialFileName($object, $params);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }

    private function generateActFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        /** @var ForeignEventWork $object */
        $date = $object->begin_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename =
            'М.'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_DOC) + $params['counter']).
            '_Пр.'.$new_date.'_'.$params['number'];
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);
        return $res . '.' . $object->actFiles[$params['counter'] - 1]->extension;
    }

    private function generateAchievementFileName($object, $params = [])
    {
        /** @var ForeignEventForm $object */
        $date = $object->startDate;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename =
            'Д.'.$new_date.'_'.$object->name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);
        return $res . '.' . $object->doc->extension;
    }

    private function generateMaterialFileName($object, $params = [])
    {
        /** @var EventParticipantForm $object */
        $date = $object->getEventStartDate();
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename =
            $object->getParticipantSurname().'_'.$new_date.'_'.$object->getForeignEventName();
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);
        return $res . '.' . $object->fileMaterial->extension;
    }
}