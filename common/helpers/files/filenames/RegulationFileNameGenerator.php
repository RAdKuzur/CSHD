<?php

namespace common\helpers\files\filenames;

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use frontend\models\work\regulation\RegulationWork;
use InvalidArgumentException;

class RegulationFileNameGenerator implements FileNameGeneratorInterface
{
    public function generateFileName($object, $fileType, $params = []): string
    {
        switch ($fileType) {
            case FilesHelper::TYPE_SCAN:
                return $this->generateScanFileName($object, $params);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }

    public function getOrdinalFileNumber($object, $fileType)
    {
        // TODO: Implement getOrdinalFileNumber() method.
    }

    private function generateScanFileName($object, $params)
    {
        /** @var RegulationWork $object */
        $new_date = DateFormatter::format($object->date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        if ($object->short_name !== '') {
            $filename = $new_date.'_'.$object->short_name;
        }
        else {
            $filename = $new_date.'_'.$object->name;
        }
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->scanFile->extension;
    }
}