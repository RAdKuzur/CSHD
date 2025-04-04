<?php
namespace frontend\events\expire;
use common\events\EventInterface;
use common\repositories\expire\ExpireRepository;
use Yii;

class ExpireCreateEvent implements EventInterface
{
    public $active_regulation_id;
    public $expire_regulation_id;
    public $expire_order_id;
    public $document_type;
    public $expire_type;
    public ExpireRepository $repository;
    public function __construct(
        $active_regulation_id,
        $expire_regulation_id,
        $expire_order_id,
        $document_type,
        $expire_type
    )
    {
        $this->active_regulation_id = $active_regulation_id;
        $this->expire_regulation_id = $expire_regulation_id;
        $this->expire_order_id = $expire_order_id;
        $this->document_type = $document_type;
        $this->expire_type = $expire_type;
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
            $this->repository->prepareCreate(
                $this->active_regulation_id,
                $this->expire_regulation_id ,
                $this->expire_order_id ,
                $this->document_type ,
                $this->expire_type
            )
        ];
    }
}