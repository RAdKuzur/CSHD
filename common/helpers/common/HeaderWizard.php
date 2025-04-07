<?php

namespace common\helpers\common;

class HeaderWizard
{
    public static function setFileHeaders(string $filename, $filesize)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $filesize);
        self::headerHelp();
        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $filesize);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // получаем содержимое как строку
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // следовать редиректам

        $data = curl_exec($ch);

        if(curl_errno($ch)) {
            echo 'Ошибка curl: ' . curl_error($ch);
            exit;
        }

        curl_close($ch);

// Заголовки для отдачи файла
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($data));

// Отдаем файл
        echo $data;
        exit;*/
        /*
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);*/
    }
    public static function headerHelp()
    {
        ob_clean();
        flush();
    }
    public static function setCsvLoadHeaders(string $filename)
    {
        header('Content-Type: text/csv;charset=windows-1251');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
    }

    public static function setZipLoadHeaders(string $archiveName)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=".basename($archiveName).";" );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($archiveName));
    }

    public static function setExcelLoadHeaders(string $filename)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
    }

    public static function setWordLoadHeaders(string $filename)
    {
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
    }
}