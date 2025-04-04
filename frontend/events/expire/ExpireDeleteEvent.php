<?php

namespace app\events\expire;

use common\events\EventInterface;
use common\repositories\expire\ExpireRepository;
use Yii;

class ExpireDeleteEvent implements EventInterface
{
    public $id;
    public ExpireRepository $repository;
    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->repository = Yii::createObject(ExpireRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        // TODO: Implement execute() method.
        return [
            $this->repository->prepareDelete($this->id)
            ];
    }
}