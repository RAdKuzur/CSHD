<?php


namespace common\services\general\files\download;

use common\services\general\files\YandexDiskContext;

class FileDownloadYandexDisk extends AbstractFileDownload
{
    const ADDITIONAL_PATH = 'DSSD/'; //дополнительный путь к папке на яндекс диске

    function __construct($tFilepath)
    {
        $this->filepath = $tFilepath;
    }
    
    public function LoadFile()
    {
        $res = YandexDiskContext::GetFileFromDisk(self::ADDITIONAL_PATH.$this->filepath);
        $this->file = $res;
        $this->success = true;
    }
}