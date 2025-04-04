<?php

namespace console\controllers\copy;

use common\helpers\files\FilesHelper;
use common\models\scaffold\DocumentIn;
use console\helper\FileTransferHelper;
use frontend\models\work\CertificateTemplatesWork;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\document_in_out\DocumentOutWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\event\EventWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\regulation\RegulationWork;
use Yii;
use yii\console\Controller;

class FilesCopyController extends Controller
{
    private FileTransferHelper $fileTransferHelper;
    public function __construct(
        $id,
        $module,
        FileTransferHelper $fileTransferHelper,
        $config = [])
    {
        $this->fileTransferHelper = $fileTransferHelper;
        parent::__construct($id, $module, $config);
    }

    public function actionDocumentInFilesCopy(){
        $documents = Yii::$app->old_db->createCommand("SELECT * FROM document_in")->queryAll();
        foreach ($documents as $document){
            if ($document['applications'] != NULL) {
                $files =
                    [
                        'table_name' => DocumentInWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_APP,
                        'files' => array_filter(explode(' ', $document['applications'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
            if ($document['doc'] != NULL) {
                $files =
                    [
                        'table_name' => DocumentInWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_DOC,
                        'files' => array_filter(explode(' ', $document['doc'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
            if ($document['scan'] != NULL) {
                $files =
                    [
                        'table_name' => DocumentInWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_SCAN,
                        'files' => array_filter(explode(' ', $document['scan'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
    }
    public function actionDocumentOutFilesCopy(){
        $documents = Yii::$app->old_db->createCommand("SELECT * FROM document_out")->queryAll();
        foreach ($documents as $document){
            if ($document['applications'] != NULL) {
                $files =
                    [
                        'table_name' => DocumentOutWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_APP,
                        'files' => array_filter(explode(' ', $document['applications'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
            if ($document['doc'] != NULL) {
                $files =
                    [
                        'table_name' => DocumentOutWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_DOC,
                        'files' => array_filter(explode(' ', $document['doc'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
            if ($document['Scan'] != NULL) {
                $files =
                    [
                        'table_name' => DocumentOutWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_SCAN,
                        'files' => array_filter(explode(' ', $document['Scan'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
    }
    public function actionDocumentOrderFilesCopy()
    {
        $documents = Yii::$app->old_db->createCommand("SELECT * FROM document_order")->queryAll();
        foreach ($documents as $document){
            if ($document['doc'] != NULL) {
                $files =
                    [
                        'table_name' => DocumentOrderWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_DOC,
                        'files' => array_filter(explode(' ', $document['doc'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
            if ($document['scan'] != NULL) {
                $files =
                    [
                        'table_name' => DocumentOrderWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_SCAN,
                        'files' => array_filter(explode(' ', $document['scan'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
    }
    public function actionRegulationFilesCopy(){
        $documents = Yii::$app->old_db->createCommand("SELECT * FROM regulation")->queryAll();
        foreach ($documents as $document){
            if ($document['scan'] != NULL) {
                $files =
                    [
                        'table_name' => RegulationWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_SCAN,
                        'files' => array_filter(explode(' ', $document['scan'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
    }
    public function actionCertificateTemplatesFilesCopy(){
        $documents = Yii::$app->old_db->createCommand("SELECT * FROM certificat_templates")->queryAll();
        foreach ($documents as $document){
            if ($document['path'] != NULL) {
                $files =
                    [
                        'table_name' => CertificateTemplatesWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_OTHER,
                        'files' => array_filter(explode(' ', $document['path'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
    }
    public function actionEventFilesCopy(){
        $documents = Yii::$app->old_db->createCommand("SELECT * FROM event")->queryAll();
        foreach ($documents as $document){
            if ($document['protocol'] != NULL) {
                $files =
                    [
                        'table_name' => EventWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_PROTOCOL,
                        'files' => array_filter(explode(' ', $document['protocol'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
        foreach ($documents as $document){
            if ($document['photos'] != NULL) {
                $files =
                    [
                        'table_name' => EventWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_PHOTO,
                        'files' => array_filter(explode(' ', $document['photos'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
        foreach ($documents as $document){
            if ($document['reporting_doc'] != NULL) {
                $files =
                    [
                        'table_name' => EventWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_REPORT,
                        'files' => array_filter(explode(' ', $document['reporting_doc'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
        foreach ($documents as $document){
            if ($document['other_files'] != NULL) {
                $files =
                    [
                        'table_name' => EventWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_OTHER,
                        'files' => array_filter(explode(' ', $document['other_files'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
    }
    public function actionForeignEventFilesCopy(){
        $documents = Yii::$app->old_db->createCommand("SELECT * FROM foreign_event")->queryAll();
        foreach ($documents as $document){
            if ($document['docs_achievement'] != NULL) {
                $files =
                    [
                        'table_name' => ForeignEventWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_DOC,
                        'files' => array_filter(explode(' ', $document['docs_achievement'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
    }
    public function actionTrainingGroupFilesCopy(){
        $documents = Yii::$app->old_db->createCommand("SELECT * FROM training_group")->queryAll();
        foreach ($documents as $document){
            if ($document['photos'] != NULL) {
                $files =
                    [
                        'table_name' => TrainingGroupWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_PHOTO,
                        'files' => array_filter(explode(' ', $document['photos'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
            if ($document['work_data'] != NULL) {
                $files =
                    [
                        'table_name' => TrainingGroupWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_MATERIAL,
                        'files' => array_filter(explode(' ', $document['work_data'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
            if ($document['present_data'] != NULL) {
                $files =
                    [
                        'table_name' => TrainingGroupWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_MATERIAL,
                        'files' => array_filter(explode(' ', $document['present_data'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
    }
    public function actionTrainingProgramFilesCopy(){
        $documents = Yii::$app->old_db->createCommand("SELECT * FROM training_program")->queryAll();
        foreach ($documents as $document){
            if ($document['doc_file'] != NULL) {
                $files =
                    [
                        'table_name' => TrainingProgramWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_DOC,
                        'files' => array_filter(explode(' ', $document['doc_file'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
            if ($document['edit_docs'] != NULL) {
                $files =
                    [
                        'table_name' => TrainingProgramWork::tableName(),
                        'table_row_id' => $document['id'],
                        'file_type' => FilesHelper::TYPE_DOC,
                        'files' => array_filter(explode(' ', $document['edit_docs'])),
                    ];
                $this->fileTransferHelper->createFile($files);
            }
        }
    }

    public function actionDeleteDocumentInFiles(){
        Yii::$app->db->createCommand()->delete('files', ['table_name' => DocumentInWork::tableName()])->execute();
    }
    public function actionDeleteDocumentOutFiles(){
        Yii::$app->db->createCommand()->delete('files', ['table_name' => DocumentOutWork::tableName()])->execute();
    }
    public function actionDeleteDocumentOrderFiles(){
        Yii::$app->db->createCommand()->delete('files', ['table_name' => DocumentOrderWork::tableName()])->execute();
    }
    public function actionDeleteRegulationFiles(){
        Yii::$app->db->createCommand()->delete('files', ['table_name' => RegulationWork::tableName()])->execute();
    }
    public function actionDeleteCertificateTemplatesFiles(){
        Yii::$app->db->createCommand()->delete('files', ['table_name' => CertificateTemplatesWork::tableName()])->execute();
    }
    public function actionDeleteEventFiles(){
        Yii::$app->db->createCommand()->delete('files', ['table_name' => EventWork::tableName()])->execute();
    }
    public function actionDeleteForeignEventFiles(){
        Yii::$app->db->createCommand()->delete('files', ['table_name' => ForeignEventWork::tableName()])->execute();
    }
    public function actionDeleteTrainingGroupFiles(){
        Yii::$app->db->createCommand()->delete('files', ['table_name' => TrainingGroupWork::tableName()])->execute();
    }
    public function actionDeleteTrainingProgramFiles(){
        Yii::$app->db->createCommand()->delete('files', ['table_name' => TrainingProgramWork::tableName()])->execute();
    }

    public function actionCopyAll(){
        $this->actionDocumentInFilesCopy();
        $this->actionDocumentOutFilesCopy();
        $this->actionDocumentOrderFilesCopy();
        $this->actionRegulationFilesCopy();
        $this->actionCertificateTemplatesFilesCopy();
        $this->actionEventFilesCopy();
        $this->actionForeignEventFilesCopy();
        $this->actionTrainingGroupFilesCopy();
        $this->actionTrainingProgramFilesCopy();
    }
    public function actionDeleteAll(){
        $this->actionDeleteDocumentInFiles();
        $this->actionDeleteDocumentOutFiles();
        $this->actionDeleteDocumentOrderFiles();
        $this->actionDeleteRegulationFiles();
        $this->actionDeleteCertificateTemplatesFiles();
        $this->actionDeleteEventFiles();
        $this->actionDeleteForeignEventFiles();
        $this->actionDeleteTrainingGroupFiles();
        $this->actionDeleteTrainingProgramFiles();
    }
}