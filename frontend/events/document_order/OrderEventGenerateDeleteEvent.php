<?php

namespace app\events\document_order;

use common\events\EventInterface;
use common\models\scaffold\OrderEventGenerate;
use common\repositories\order\OrderEventGenerateRepository;

class OrderEventGenerateDeleteEvent implements EventInterface
{
    private $id;
    private OrderEventGenerateRepository $orderEventGenerateRepository;
    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->orderEventGenerateRepository = new OrderEventGenerateRepository();

    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute()
    {
        // TODO: Implement execute() method.
        return
            [
                $this->orderEventGenerateRepository->prepareDeleteByOrder($this->id),
            ];
    }
}