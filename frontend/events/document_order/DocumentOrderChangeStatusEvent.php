<?php

namespace app\events\document_order;

use common\events\EventInterface;
use common\repositories\order\DocumentOrderRepository;

class DocumentOrderChangeStatusEvent implements EventInterface
{
    private $id;
    private DocumentOrderRepository $documentOrderRepository;
    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->documentOrderRepository = new DocumentOrderRepository();
    }

    public function isSingleton(): bool
    {
        return false;
    }
    public function execute(){
        return [
            $this->documentOrderRepository->prepareChangeStatus($this->id)
        ];
    }
}