<?php

namespace frontend\invokables;

use common\helpers\common\HeaderWizard;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ProtocolLoader
{
    private PhpWord $data;
    private string $filename;

    public function __construct(
        PhpWord $data,
        string $filename
    )
    {
        $this->data = $data;
        $this->filename = $filename;
    }

    public function __invoke()
    {
        HeaderWizard::setWordLoadHeaders($this->filename);
        $writer = IOFactory::createWriter($this->data);
        $writer->save("php://output");
        exit;
    }
}