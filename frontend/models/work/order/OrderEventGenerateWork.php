<?php

namespace app\models\work\order;

use common\models\scaffold\OrderEventGenerate;

class OrderEventGenerateWork extends OrderEventGenerate
{
    public static function fill(
        $orderId,
        $purpose,
        $docEvent,
        $respPeopleInfo,
        $timeProvisionDay,
        $extraRespInsert,
        $timeInsertDay,
        $extraRespMethod,
        $extraRespInfoStuff,
        $documentDetails = null

    ){
        $entity = new static();
        $entity->order_id = $orderId;
        $entity->purpose = $purpose;
        $entity->doc_event = $docEvent;
        $entity->resp_people_info_id = $respPeopleInfo;
        $entity->time_provision_day = $timeProvisionDay;
        $entity->extra_resp_insert_id = $extraRespInsert;
        $entity->time_insert_day = $timeInsertDay;
        $entity->extra_resp_method_id = $extraRespMethod;
        $entity->extra_resp_info_stuff_id = $extraRespInfoStuff;
        $entity->document_details = $documentDetails;
        return $entity;
    }
    public function fillUpdate(
        $orderId,
        $purpose,
        $docEvent,
        $respPeopleInfo,
        $timeProvisionDay,
        $extraRespInsert,
        $timeInsertDay,
        $extraRespMethod,
        $extraRespInfoStuff,
        $documentDetails = null

    ){
        $this->order_id = $orderId;
        $this->purpose = $purpose;
        $this->doc_event = $docEvent;
        $this->resp_people_info_id = $respPeopleInfo;
        $this->time_provision_day = $timeProvisionDay;
        $this->extra_resp_insert_id = $extraRespInsert;
        $this->time_insert_day = $timeInsertDay;
        $this->extra_resp_method_id = $extraRespMethod;
        $this->extra_resp_info_stuff_id = $extraRespInfoStuff;
        $this->document_details = $documentDetails;
    }
}