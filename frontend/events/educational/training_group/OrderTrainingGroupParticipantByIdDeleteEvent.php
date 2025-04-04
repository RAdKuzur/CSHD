<?php

namespace app\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;

class OrderTrainingGroupParticipantByIdDeleteEvent implements EventInterface
{
    private $id;
    private OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;
    public function __construct(
        $id
    )
    {
        $this->id = $id;
        $this->orderTrainingGroupParticipantRepository = new OrderTrainingGroupParticipantRepository();
    }
    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->orderTrainingGroupParticipantRepository->prepareDeleteById($this->id),
        ];
    }
}