<?php

namespace common\components\wizards;

use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Yii;

class ExcelWizard
{
    /**
     * Возвращает массив данных из столбца с заданным заголовком
     * @param Worksheet $worksheet текущий лист файла
     * @param string $header искомый заголовок
     * @param int $headerRow номер строки с заголовками
     * @return array
     */
    public static function getColumnDataByHeader(Worksheet $worksheet, string $header, int $headerRow = 1)
    {
        $highestRow = $worksheet->getHighestRow();
        $highestCol = $worksheet->getHighestColumn();

        $columnIndex = 1;
        $tempValue = $worksheet->getCell(Coordinate::stringFromColumnIndex($columnIndex) . $headerRow)->getValue();
        while (Coordinate::stringFromColumnIndex($columnIndex) < $highestCol && $tempValue !== $header) {
            $columnIndex++;
            $tempValue = $worksheet->getCell(Coordinate::stringFromColumnIndex($columnIndex) . $headerRow)->getValue();
        }

        $data = [];
        $mainIndex = 0;
        while ($mainIndex < $highestRow - $headerRow) {
            $data[] = $worksheet->getCell(Coordinate::stringFromColumnIndex($columnIndex) . ($headerRow + $mainIndex + 1))->getFormattedValue()/*$row*/;
            $mainIndex++;
        }

        return $data;
    }

    /**
     * Возвращает массив с данными по выбранным столбцам из Excel-файла
     * @param $filepath
     * @param array $columns массив строк-заголовков столбцов
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDataFromColumns($filepath, array $columns)
    {
        ini_set('memory_limit', '512M');

        $reader = new Xlsx();
        $spreadsheet = $reader->load($filepath);
        $worksheet = $spreadsheet->setActiveSheetIndex(0);
        $highestRow = $worksheet->getHighestRow();

        $startRow = 1;
        $tempValue = $worksheet->getCell(Coordinate::stringFromColumnIndex(1) . $startRow);
        while ($startRow < $highestRow && strlen($tempValue) < 1) {
            $startRow++;
            $tempValue = $worksheet->getCell(Coordinate::stringFromColumnIndex(1) . $startRow)->getValue();
        }

        $headers = $columns;
        $data = [];
        foreach ($headers as $header) {
            $data[$header] = self::getColumnDataByHeader($worksheet, $header, $startRow);
        }

        return $data;
    }
}