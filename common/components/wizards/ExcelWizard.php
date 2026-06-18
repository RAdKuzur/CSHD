<?php

namespace common\components\wizards;

use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
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

        $highestColIndex = Coordinate::columnIndexFromString($highestCol);
        $columnIndex = null;

        for ($i = 1; $i <= $highestColIndex; $i++) {
            $value = trim((string)$worksheet
                ->getCell(Coordinate::stringFromColumnIndex($i) . $headerRow)
                ->getValue());

            if ($value === trim($header)) {
                $columnIndex = $i;
                break;
            }
        }

        $data = [];

        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $data[] = $worksheet
                ->getCell(Coordinate::stringFromColumnIndex($columnIndex) . $row)
                ->getFormattedValue();
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

        // Получаем расширение файла в нижнем регистре
        $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        // Создаем reader в зависимости от расширения
        if ($extension === 'xlsx') {
            $reader = new Xlsx();
        } else {
            $reader = new Xls();
        }
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
    public static function getDataFromColumnUtpFiles(Worksheet $worksheet, array $columns, $rows)
    {
        ini_set('memory_limit', '512M');
        $data = [];
        for($i = 2; $i <= $rows; $i++) {
            foreach ($columns as $j => $column) {
                $data[$column][] = $worksheet->getCell(Coordinate::stringFromColumnIndex($j + 1) . $i)->getValue();;
            }
        }
        return $data;
    }
    public static function getDataFromUtpFiles($filepath, array $columns)
    {
        ini_set('memory_limit', '512M');

        $reader = new Xlsx();
        $spreadsheet = $reader->load($filepath);
        $worksheet = $spreadsheet->setActiveSheetIndex(0);
        $highestRow = $worksheet->getHighestRow();

        $startRow = 1;
        $tempValue = $worksheet->getCell(Coordinate::stringFromColumnIndex(1) . $startRow)->getValue();
        while ($startRow < $highestRow && strlen($tempValue) > 0) {
            $startRow++;
            $tempValue = $worksheet->getCell(Coordinate::stringFromColumnIndex(1) . $startRow)->getValue();
        }
        $data = self::getDataFromColumnUtpFiles($worksheet, $columns, $startRow);
        return $data;
    }
}