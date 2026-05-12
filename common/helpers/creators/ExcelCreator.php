<?php

namespace common\helpers\creators;

use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelCreator
{
    /**
     * Формирует csv-файл из матрицы строковых данных
     *
     * @param string[][] $data
     * @param string[] $headers
     * @return Spreadsheet
     * @throws Exception
     */
    public static function createCsvFile(array $data, array $headers) : Spreadsheet
    {
        $file = new Spreadsheet();
        $worksheet = $file->getActiveSheet();
        $worksheet->fromArray($headers);

        $rowIndex = 2;
        foreach ($data as $row) {
            $worksheet->fromArray(
                $row,
                null ,
                'A'.$rowIndex++);
        }

        return $file;
    }

    /**
     * Создаёт книгу Excel с несколькими листами.
     * 
     * @param array $sheetsData Массив вида: [ 'Название листа' => [ ['Заголовок1','Заголовок2'], [данные строки1], ... ] ]
     * @return Spreadsheet
     * @throws Exception
     */
    public static function createMultiSheetExcel(array $sheetsData): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($sheetsData as $sheetTitle => $sheetData) {
            $worksheet = new Worksheet($spreadsheet, $sheetTitle);
            $spreadsheet->addSheet($worksheet);

            if (empty($sheetData)) {
                $worksheet->setCellValue('A1', 'Нет данных');
                continue;
            }

            $headers = array_shift($sheetData);
            $worksheet->fromArray($headers, null, 'A1');

            $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
            $headerRange = 'A1:' . $lastColumn . '1';
            $worksheet->getStyle($headerRange)->getFont()->setBold(true);
            $worksheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); 
            $worksheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $rowIndex = 2;
            foreach ($sheetData as $row) {
                $colIndex = 1;
                foreach ($row as $cellValue) {
                    $cellCoordinate = Coordinate::stringFromColumnIndex($colIndex) . $rowIndex;
                    if (is_array($cellValue) && isset($cellValue['url'])) {
                        $worksheet->setCellValue($cellCoordinate, $cellValue['text']);
                        $worksheet->getCell($cellCoordinate)
                                ->getHyperlink()
                                ->setUrl($cellValue['url']);
                    } else {
                        $worksheet->setCellValue($cellCoordinate, $cellValue);
                    }
                    $colIndex++;
                }
                $rowIndex++;
            }

            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            for ($i = 1; $i <= $highestColumnIndex; $i++) {
                $columnName = Coordinate::stringFromColumnIndex($i);
                $worksheet->getColumnDimension($columnName)->setAutoSize(true);
            }

            $lastColumnHeader = Coordinate::stringFromColumnIndex(count($headers));
            $worksheet->setAutoFilter('A1:' . $lastColumnHeader . '1');

            $worksheet->freezePane('A2');
        }

        return $spreadsheet;
    }

    /**
     * Сохраняет книгу Excel в файл
     *
     * @param Spreadsheet $spreadsheet
     * @param string $filePath
     * @throws Exception
     */
    public static function saveSpreadsheet(Spreadsheet $spreadsheet, string $filePath): void
    {
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }
}