<?php

namespace frontend\events\document_out;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use Yii;

class InOutDocumentUpdateLinkEvent implements EventInterface
{
    private $docInId;
    private $docOutId;
    private InOutDocumentsRepository $inOutDocumentsRepository;
    public function __construct(
        $docInId,
        $docOutId
    )
    {
        $this->docInId = $docInId;
        $this->docOutId = $docOutId;
        $this->inOutDocumentsRepository = Yii::createObject(InOutDocumentsRepository::class);
    }

    public function isSingleton(): bool
    {
        return true;
    }
    public function execute(){
        return [
            $this->inOutDocumentsRepository->prepareUpdateLink($this->docInId, $this->docOutId),
        ];
    }
}