<?php

namespace app\services\order;

use app\models\work\order\OrderEventGenerateWork;

use common\services\general\PeopleStampService;

class OrderEventGenerateService
{
    private PeopleStampService $peopleStampService;
    public function __construct(
        PeopleStampService $peopleStampService
    )
    {
        $this->peopleStampService = $peopleStampService;
    }

    public function setPeopleStamp(OrderEventGenerateWork $model){
        if ($model->extra_resp_info_stuff_id != ""){
            $model->extra_resp_info_stuff_id = $this->peopleStampService->createStampFromPeople($model->extra_resp_info_stuff_id);
        }
        if ($model->extra_resp_method_id != ""){
            $model->extra_resp_method_id =  $this->peopleStampService->createStampFromPeople($model->extra_resp_method_id);
        }
        if ($model->extra_resp_insert_id != ""){
            $model->extra_resp_insert_id = $this->peopleStampService->createStampFromPeople($model->extra_resp_insert_id);
        }
        if ($model->resp_people_info_id != ""){
            $model->resp_people_info_id = $this->peopleStampService->createStampFromPeople($model->resp_people_info_id);
        }
    }
}