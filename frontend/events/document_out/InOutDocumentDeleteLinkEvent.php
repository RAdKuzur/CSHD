<?php

namespace frontend\events\document_out;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use Yii;

class InOutDocumentDeleteLinkEvent implements EventInterface
{
    private $docOutId;
    private InOutDocumentsRepository $inOutDocumentsRepository;
    public function __construct(
        $docOutId
    )
    {
        $this->docOutId = $docOutId;
        $this->inOutDocumentsRepository = Yii::createObject(InOutDocumentsRepository::class);
    }

    public function isSingleton(): bool
    {
        return true;
    }
    public function execute(){
        return [
            $this->inOutDocumentsRepository->prepareDeleteLink($this->docOutId),
        ];
    }
}