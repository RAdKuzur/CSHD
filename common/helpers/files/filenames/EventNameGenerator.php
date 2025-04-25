<?php

namespace common\helpers\files\filenames;

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\repositories\event\EventRepository;
use common\repositories\general\FilesRepository;
use DomainException;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\general\FilesWork;
use InvalidArgumentException;

class EventNameGenerator implements FileNameGeneratorInterface
{
    private FilesRepository $filesRepository;
    private EventRepository $repository;

    public function __construct(
        FilesRepository $filesRepository,
        EventRepository $repository
    )
    {
        $this->filesRepository = $filesRepository;
        $this->repository = $repository;
    }

    public function generateFileName($object, $fileType, $params = []): string
    {
        switch ($fileType) {
            case FilesHelper::TYPE_PROTOCOL:
                return $this->generateProtocolFileName($object, $params);
            case FilesHelper::TYPE_PHOTO:
                return $this->generatePhotoFileName($object, $params);
            case FilesHelper::TYPE_REPORT:
                return $this->generateReportFileName($object, $params);
            case FilesHelper::TYPE_OTHER:
                return $this->generateOtherFileName($object, $params);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }

    private function generateProtocolFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        // Тут должна быть дата связанного приказа (но пока нет приказов - заглушка)
        // $this->orderWork->order_date;
        $date = DateFormatter::format($object->start_date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        // Использовать строку ниже после создания приказов
        // $filename = 'Пр'.$params['counter'].'_'.$date.'_'.$object->start_date.'-'.$this->orderWork->order_copy_id . ($this->orderWork->order_postfix ? : '') . '_' .$object->name . '_'.$object->getEventNumber();
        $filename = 'Пр'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_PROTOCOL) + $params['counter']).'_'.$date. '_' .$object->name . '_'.$this->repository->getEventNumber($object);
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->protocolFiles[$params['counter'] - 1]->extension;
    }

    private function generateReportFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        // Тут должна быть дата связанного приказа (но пока нет приказов - заглушка)
        // $this->orderWork->order_date;
        $date = DateFormatter::format($object->start_date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        // Использовать строку ниже после создания приказов
        // $filename = 'Яв'.$params['counter'].'_'.$date.'_'.$object->start_date.'-'.$this->orderWork->order_copy_id . ($this->orderWork->order_postfix ? : '') . '_' .$object->name . '_'.$object->getEventNumber();
        $filename = 'Яв'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_REPORT) + $params['counter']).'_'.$date. '_' .$object->name . '_'.$this->repository->getEventNumber($object);
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->reportingFiles[$params['counter'] - 1]->extension;
    }

    private function generatePhotoFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        // Тут должна быть дата связанного приказа (но пока нет приказов - заглушка)
        // $this->orderWork->order_date;
        $date = DateFormatter::format($object->start_date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        // Использовать строку ниже после создания приказов
        // $filename = 'Фото'.$params['counter'].'_'.$date.'_'.$object->start_date.'-'.$this->orderWork->order_copy_id . ($this->orderWork->order_postfix ? : '') . '_' .$object->name . '_'.$object->getEventNumber();
        $filename = 'Фото'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_PHOTO) + $params['counter']).'_'.$date.'_' .$object->name . '_'.$this->repository->getEventNumber($object);
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->photoFiles[$params['counter'] - 1]->extension;
    }

    private function generateOtherFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        // Тут должна быть дата связанного приказа (но пока нет приказов - заглушка)
        // $this->orderWork->order_date;
        $date = DateFormatter::format($object->start_date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        // Использовать строку ниже после создания приказов
        // $filename = 'Файл'.$params['counter'].'_'.$date.'_'.$object->start_date.'-'.$this->orderWork->order_copy_id . ($this->orderWork->order_postfix ? : '') . '_' .$object->name . '_'.$object->getEventNumber();
        $filename = 'Файл'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_OTHER) + $params['counter']).'_'.$date. '_' .$object->name . '_'.$this->repository->getEventNumber($object);
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->otherFiles[$params['counter'] - 1]->extension;
    }
    public function getOrdinalFileNumber($object, $fileType)
    {
        switch ($fileType) {
            case FilesHelper::TYPE_PROTOCOL:
                return $this->getOrdinalFileNumberProtocol($object);
            case FilesHelper::TYPE_PHOTO:
                return $this->getOrdinalFileNumberPhoto($object);
            case FilesHelper::TYPE_REPORT:
                return $this->getOrdinalFileNumberReport($object);
            case FilesHelper::TYPE_OTHER:
                return $this->getOrdinalFileNumberOther($object);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }
    public function getOrdinalFileNumberProtocol($object)
    {
        $lastDocFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_PROTOCOL);
        /** @var FilesWork $lastDocFile */
        if ($lastDocFile) {
            preg_match('/Пр(\d+)_/', $lastDocFile->filepath, $matches);
            return (int)$matches[1];
        }
        return 0;
    }
    public function getOrdinalFileNumberPhoto($object)
    {
        $lastDocFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_PHOTO);
        /** @var FilesWork $lastDocFile */
        if ($lastDocFile) {
            preg_match('/Фото(\d+)_/', $lastDocFile->filepath, $matches);
            return (int)$matches[1];
        }
        return 0;
    }
    public function getOrdinalFileNumberReport($object)
    {
        $lastDocFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_REPORT);
        /** @var FilesWork $lastDocFile */
        if ($lastDocFile) {
            preg_match('/Яв(\d+)_/', $lastDocFile->filepath, $matches);
            return (int)$matches[1];
        }
        return 0;
    }
    public function getOrdinalFileNumberOther($object)
    {
        $lastDocFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_OTHER);
        /** @var FilesWork $lastDocFile */
        if ($lastDocFile) {
            preg_match('/Файл(\d+)_/', $lastDocFile->filepath, $matches);
            return (int)$matches[1];
        }
        return 0;
    }
}