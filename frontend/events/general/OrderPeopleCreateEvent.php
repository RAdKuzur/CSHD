<?php

namespace frontend\events\general;

use common\events\EventInterface;
use common\repositories\general\OrderPeopleRepository;
use Yii;

class OrderPeopleCreateEvent implements EventInterface
{
    public $people_id;
    public $order_id;
    public OrderPeopleRepository $repository;

    public function __construct(
        $people_id,
        $order_id
    )
    {
        $this->people_id = $people_id;
        $this->order_id = $order_id;
        $this->repository = Yii::createObject(OrderPeopleRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        // TODO: Implement execute() method.
        return [
            $this->repository->prepareCreate(
                $this->people_id,
                $this->order_id
            )
        ];
    }
}