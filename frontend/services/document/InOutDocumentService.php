<?php

namespace frontend\services\document;

use common\repositories\document_in_out\InOutDocumentsRepository;
use DomainException;
use frontend\models\work\document_in_out\InOutDocumentsWork;

class InOutDocumentService
{
    private InOutDocumentsRepository $inOutDocumentRepository;
    public function __construct(
        InOutDocumentsRepository $inOutDocumentRepository
    )
    {
        $this->inOutDocumentRepository = $inOutDocumentRepository;
    }
    public function deleteLink($documentOutId){
        /* @var $model InOutDocumentsWork */
        $model = $this->inOutDocumentRepository->getByDocumentOutId($documentOutId);
        if($model) {
            $model->document_out_id = null;
            $this->inOutDocumentRepository->save($model);
        }
    }
    public function updateLink($docInId, $docOutId){
        /* @var $model InOutDocumentsWork */
        $model = $this->inOutDocumentRepository->getByDocumentInId($docInId);
        if ($model === null) {
            throw new DomainException("Не найдена запись в in_out_documents для document_in {$docInId}");
        }
        $model->document_out_id = $docOutId;
        $this->inOutDocumentRepository->save($model);
    }
}