<?php

namespace common\repositories\document_in_out;

use common\components\traits\CommonDatabaseFunctions;
use common\helpers\files\FilesHelper;
use common\models\scaffold\Files;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\document_in\InOutDocumentDeleteEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\document_in_out\DocumentOutWork;
use frontend\models\work\document_in_out\InOutDocumentsWork;
use yii\db\ActiveRecord;

class DocumentOutRepository
{
    use CommonDatabaseFunctions;

    private FileService $fileService;
    private FilesRepository $filesRepository;

    public function __construct(
        FileService $fileService,
        FilesRepository $filesRepository
    )
    {
        $this->fileService = $fileService;
        $this->filesRepository = $filesRepository;
    }
    public function get($id)
    {
        return DocumentOutWork::find()->where(['id' => $id])->one();
    }
    public function setAnswer($model)
    {
        return $model->isAnswer;
    }
    public function createReserve(DocumentOutWork $model)
    {

        $model->document_name = 'NAME';
        $model->sent_date = date('Y-m-d');
        $model->document_theme = 'Резерв';
    }

    public function findFileDocuments($id)
    {
        $InOutDocument = Files::find()->where(['id' => $id, 'table_name' => 'document_in'])->all();
        return $InOutDocument[0]->table_row_id;
    }
    public function matchInOutDocuments($DocumentInId,$DocumentOutId )
    {
        $model = InOutDocumentsWork::find()->where(['document_in_id' => $DocumentInId])->one();
        $model->document_out_id = $DocumentOutId;
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения исходящего документа. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
    public function getDocumentInWithoutAnswer()
    {
        $model = [];
        $docs = InOutDocumentsWork::find()->where(['document_out_id' => null])->all();
        foreach ($docs as $doc) {
            $model[] = DocumentInWork::find()->where(['id' => $doc->document_in_id])->one();
        }
        return $model;
    }
    public function getAllDocumentsDescDate()
    {
        return DocumentOutWork::find()->orderBy(['document_date' => SORT_DESC])->all();
    }

    public function getAllDocumentsInYear()
    {
        return DocumentOutWork::find()->where(['like', 'document_date', date('Y')])->orderBy(['document_number' => SORT_ASC, 'document_postfix' => SORT_ASC])->all();
    }

    public function save(DocumentOutWork $document)
    {
        if (!$document->save()) {
            throw new DomainException('Ошибка сохранения исходящего документа. Проблемы: '.json_encode($document->getErrors()));
        }
        return $document->id;
    }

    public function delete(ActiveRecord $model)
    {
        /** @var DocumentOutWork $model */
        $model->recordEvent(new InOutDocumentDeleteEvent($model->id), DocumentOutWork::class);
        $scan = $this->filesRepository->get(DocumentOutWork::tableName(), $model->id, FilesHelper::TYPE_SCAN);
        $docs = $this->filesRepository->get(DocumentOutWork::tableName(), $model->id, FilesHelper::TYPE_DOC);
        $apps = $this->filesRepository->get(DocumentOutWork::tableName(), $model->id, FilesHelper::TYPE_APP);

        if (is_array($scan)) {
            foreach ($scan as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($docs)) {
            foreach ($docs as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($apps)) {
            foreach ($apps as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        $model->recordEvent(new InOutDocumentDeleteEvent($model->id), get_class($model));

        $model->releaseEvents();

        return $model->delete();
    }
    public function findUpNumber($year, $document_date){
        return DocumentOutWork::find()
            ->where(['>', 'document_date', $document_date])
            ->andWhere(['>=', 'document_date', $year."-01-01"])
            ->andWhere(['<=', 'document_date', $year."-12-31"])
            ->orderBy(['document_date' => SORT_DESC])
            ->all();
    }
    public function findDownNumber($year, $document_date){
        return DocumentOutWork::find()
            ->where(['<=', 'document_date', $document_date]) // условие для даты больше заданной
            ->andWhere(['>=', 'document_date', $year."-01-01"]) // начало года
            ->andWhere(['<=', 'document_date', $year."-12-31"]) // конец года
            ->orderBy(['document_date' => SORT_DESC])
            ->all();
    }
    public function findMaxDownNumber($year, $document_date)
    {
        return DocumentOutWork::find()
            ->where(['<=', 'document_date', $document_date])
            ->andWhere(['>=', 'document_date', $year."-01-01"])
            ->andWhere(['<=', 'document_date', $year."-12-31"])
            ->max('document_number');
    }
    public function findMaxPostfix($year, $document_number)
    {
        return DocumentOutWork::find()
            ->where(['<=', 'document_number', $document_number])
            ->andWhere(['>=', 'document_date', $year."-01-01"]) // начало года
            ->andWhere(['<=', 'document_date', $year."-12-31"]) // конец года
            ->max('document_postfix');
    }
}