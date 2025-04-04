<?php
namespace frontend\events\educational\training_group;
use common\events\EventInterface;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use Yii;
class DeleteOrderTrainingGroupParticipantEvent implements EventInterface
{
    private $trainingGroupParticipantOutId;
    private $trainingGroupParticipantInId;
    private $orderId;
    private OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;
    public function __construct(
        $trainingGroupParticipantOutId,
        $trainingGroup_participantInId,
        $orderId
    )
    {
        $this->trainingGroupParticipantOutId = $trainingGroupParticipantOutId;
        $this->trainingGroupParticipantInId = $trainingGroup_participantInId;
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
            $this->orderTrainingGroupParticipantRepository->prepareDelete(
                $this->trainingGroupParticipantOutId,
                $this->trainingGroupParticipantInId,
                $this->orderId
            )
        ];
    }

}