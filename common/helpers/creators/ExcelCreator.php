<?php

namespace common\helpers\creators;

use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
}