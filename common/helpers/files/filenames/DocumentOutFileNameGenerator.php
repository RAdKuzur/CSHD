<?php

namespace common\helpers\files\filenames;

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\repositories\general\FilesRepository;
use DomainException;
use frontend\models\work\document_in_out\DocumentOutWork;
use frontend\models\work\general\FilesWork;
use InvalidArgumentException;

class DocumentOutFileNameGenerator implements FileNameGeneratorInterface
{
    private FilesRepository $filesRepository;

    public function __construct(FilesRepository $filesRepository)
    {
        $this->filesRepository = $filesRepository;
    }

    public function generateFileName($object, $fileType, $params = []): string
    {
        switch ($fileType) {
            case FilesHelper::TYPE_SCAN:
                return $this->generateScanFileName($object, $params);
            case FilesHelper::TYPE_DOC:
                return $this->generateDocFileName($object, $params);
            case FilesHelper::TYPE_APP:
                return $this->generateAppFileName($object, $params);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
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
            preg_match('/Приложение(\d+)_/', basename($lastAppFile->filepath), $matches);
            return (int)$matches[1];
        }

        return 0;
    }

    private function generateDocFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        /** @var DocumentOutWork $object */
        $date = $object->document_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        if ($object->companyWork->short_name !== '') {
            $filename =
                'Ред'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_DOC) + $params['counter']).
                '_Исх.'.$new_date.'_'.$object->document_number.'_'.$object->companyWork->short_name.'_'.$object->document_theme;
        }
        else {
            $filename =
                'Ред'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_DOC) + $params['counter']).
                '_Исх.'.$new_date.'_'.$object->document_number.'_'.$object->companyWork->name.'_'.$object->document_theme;
        }
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->docFile[$params['counter'] - 1]->extension;
    }

    private function generateScanFileName($object, $params = [])
    {
        /** @var DocumentOutWork $object */
        $date = $object->document_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        if ($object->companyWork->short_name !== '') {
            $filename = 'Исх.'.$new_date.'_'.$object->document_number.'_'.$object->companyWork->short_name.'_'.$object->document_theme;
        }
        else {
            $filename = 'Исх.'.$new_date.'_'.$object->document_number.'_'.$object->companyWork->name.'_'.$object->document_theme;
        }
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->scanFile->extension;
    }

    private function generateAppFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        /** @var DocumentOutWork $object */
        $date = $object->document_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        if ($object->company->short_name !== '') {
            $filename = 'Приложение'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_APP) + $params['counter']).'_Вх.'.$new_date.'_'.$object->document_number.'_'.$object->companyWork->short_name.'_'.$object->document_theme;
        }
        else {
            $filename = 'Приложение'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_APP) + $params['counter']).'_Вх.'.$new_date.'_'.$object->document_number.'_'.$object->companyWork->name.'_'.$object->document_theme;
        }
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->appFile[$params['counter'] - 1]->extension;
    }
}