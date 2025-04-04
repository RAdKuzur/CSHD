<?php

namespace backend\invokables;

use common\helpers\common\HeaderWizard;
use common\helpers\creators\ExcelCreator;
use Hidehalo\Nanoid\Client;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Yii;

/**
 * Класс для создания и выгрузки CSV-файла
 */
class CsvLoader
{
    /**
     * Данные для создания CSV-файла
     *
     * @var string[] $headers заголовки столбцов
     * @var string $data данные файла
     */
    private array $headers;
    private string $data;

    public function __construct(
        array $headers,
        string $data
    )
    {
        $this->headers = $headers;
        $this->data = $data;
    }

    public function __invoke()
    {
        $data = json_decode($this->data, true);
        $writer = new Csv(
            ExcelCreator::createCsvFile(
                $data,
                $this->headers
            )
        );

        HeaderWizard::setCsvLoadHeaders((Yii::createObject(Client::class))->generateId(10) . '.csv');
        $writer->setDelimiter(';');
        $writer->setOutputEncoding('windows-1251');
        $writer->save('php://output');
        exit;
    }
}