<?php
namespace frontend\events\educational\training_group;
use common\events\EventInterface;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use Yii;
class CreateOrderTrainingGroupParticipantEvent implements EventInterface
{
    private $trainingGroupParticipantOutId;
    private $trainingGroupParticipantInId;
    private $orderId;
    private OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;
    public function __construct(
        $trainingGroupParticipantOutId,
        $trainingGroupParticipantInId,
        $orderId
    )
    {
        $this->trainingGroupParticipantOutId = $trainingGroupParticipantOutId;
        $this->trainingGroupParticipantInId = $trainingGroupParticipantInId;
        $this->orderId = $orderId;
        $this->orderTrainingGroupParticipantRepository = Yii::createObject(OrderTrainingGroupParticipantRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->orderTrainingGroupParticipantRepository->prepareCreate(
                $this->trainingGroupParticipantOutId,
                $this->trainingGroupParticipantInId,
                $this->orderId
            )
        ];
    }
}