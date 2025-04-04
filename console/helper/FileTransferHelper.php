<?php
namespace console\helper;
use Yii;

class FileTransferHelper
{
    public function createFile($data){
        foreach ($data['files'] as $file){
            Yii::$app->db->createCommand()->insert('files',
                [
                    'table_name' => $data['table_name'],
                    'table_row_id' => $data['table_row_id'],
                    'file_type' => $data['file_type'],
                    'filepath' => $file,
                ]
            )->execute();
        }
    }
}