<?php

namespace frontend\events\document_in;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use Yii;

class InOutDocumentUpdateEvent implements EventInterface
{
    private $documentInId;
    private $documentOutId;
    private $date;
    private $responsibleId;

    private InOutDocumentsRepository $repository;

    public function __construct(
        $documentInId,
        $documentOutId = null,
        $date = null,
        $responsibleId = null
    )
    {
        $this->documentInId = $documentInId;
        $this->documentOutId = $documentOutId;
        $this->date = $date;
        $this->responsibleId = $responsibleId;
        $this->repository = Yii::createObject(InOutDocumentsRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareUpdate(
                $this->documentInId,
                $this->documentOutId,
                $this->date,
                $this->responsibleId
            )
        ];
    }
}