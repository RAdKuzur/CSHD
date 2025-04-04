<?php

namespace app\events\general;

use common\events\EventInterface;
use common\repositories\general\OrderPeopleRepository;
use Yii;

class OrderPeopleDeleteByIdEvent implements EventInterface
{

    public $id;
    public OrderPeopleRepository $repository;
    public function __construct(
        $id
    )
    {
        $this->id = $id;
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
            $this->repository->prepareDeleteById(
                $this->id,
            )
        ];
    }
}