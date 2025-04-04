<?php

namespace frontend\invokables;

use common\helpers\common\HeaderWizard;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JournalLoader
{
    private Spreadsheet $data;
    private string $filename;

    public function __construct(
        Spreadsheet $data,
        string $filename
    )
    {
        $this->data = $data;
        $this->filename = $filename;
    }

    public function __invoke()
    {
        HeaderWizard::setExcelLoadHeaders($this->filename);
        $writer = new Xlsx($this->data);
        $writer->save('php://output');
        exit;
    }
}