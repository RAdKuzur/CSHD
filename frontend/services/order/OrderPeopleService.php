<?php

namespace frontend\services\order;

use common\repositories\dictionaries\PeopleRepository;
use common\repositories\general\PeopleStampRepository;
use common\services\general\PeopleStampService;
use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\order\DocumentOrderWork;
use common\models\scaffold\DocumentOrder;
use common\repositories\general\OrderPeopleRepository;
use frontend\events\general\OrderPeopleCreateEvent;
use frontend\events\general\OrderPeopleDeleteEvent;
use yii\helpers\ArrayHelper;

class OrderPeopleService
{
    private OrderPeopleRepository $orderPeopleRepository;
    private PeopleStampService $peopleStampService;
    private PeopleStampRepository $peopleStampRepository;
    public function __construct(
        OrderPeopleRepository $orderPeopleRepository,
        PeopleStampService $peopleStampService,
        PeopleStampRepository $peopleStampRepository
    )
    {
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->peopleStampService = $peopleStampService;
        $this->peopleStampRepository = $peopleStampRepository;
    }

    public function addOrderPeopleEvent($respPeople, $model)
    {
        if (is_array($respPeople)) {
            $respPeople = array_unique($respPeople);
            foreach ($respPeople as $person) {
                if ($person != NULL) {
                    $person = $this->peopleStampService->createStampFromPeople($person);
                    if ($this->orderPeopleRepository->checkUnique($person, $model->id)) {
                        $model->recordEvent(new OrderPeopleCreateEvent($person, $model->id), OrderPeopleWork::class);
                    }
                }
            }
        }
    }
    public function deleteOrderPeopleEvent($respPeople, $model){
        if (is_array($respPeople)) {
            $respPeople = array_unique($respPeople);
            foreach ($respPeople as $person) {
                if ($person != NULL) {
                    $person = $this->peopleStampService->createStampFromPeople($person);
                    if (!$this->orderPeopleRepository->checkUnique($person, $model->id)) {
                        $model->recordEvent(new OrderPeopleDeleteEvent($person, $model->id), OrderPeopleWork::class);
                    }
                }
            }
        }
    }
    public function updateOrderPeopleEvent($respPeople, $formRespPeople ,  $model)
    {
        $respPeople = ArrayHelper::getColumn($this->peopleStampRepository->getStamps($respPeople), 'people_id');
        if($respPeople != NULL && $formRespPeople != NULL) {
            $addSquadParticipant = array_diff($formRespPeople, $respPeople);
            $deleteSquadParticipant = array_diff($respPeople, $formRespPeople);
        }
        else if($formRespPeople == NULL && $respPeople != NULL) {
            $deleteSquadParticipant = $respPeople;
            $addSquadParticipant = NULL;
        }
        else if($respPeople == NULL && $formRespPeople != NULL) {
            $addSquadParticipant = $formRespPeople;
            $deleteSquadParticipant = NULL;
        }
        else {
            $deleteSquadParticipant = NULL;
            $addSquadParticipant = NULL;
        }
        if($deleteSquadParticipant != NULL) {
            $this->deleteOrderPeopleEvent($deleteSquadParticipant, $model);
        }
        if($addSquadParticipant != NULL) {
            $this->addOrderPeopleEvent($addSquadParticipant, $model);
        }
    }
}