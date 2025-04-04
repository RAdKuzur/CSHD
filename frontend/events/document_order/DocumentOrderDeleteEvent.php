<?php

namespace app\events\document_order;

use common\events\EventInterface;
use common\repositories\order\DocumentOrderRepository;
use frontend\models\work\order\DocumentOrderWork;
use frontend\services\order\DocumentOrderService;
use Yii;

class DocumentOrderDeleteEvent implements EventInterface
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
            $this->documentOrderRepository->prepareDelete($this->id)
        ];
    }
}