<?php

namespace frontend\models\forms;



use frontend\models\work\order\ExpireWork;
use yii\base\Model;

class ExpireForm extends Model
{
    public $activeRegulationId;
    public $expireRegulationId;
    public $expireOrderId;
    public $docType;
    public $expireType;
    public static function attachAttributes($activeRegulationId, $expireRegulationId, $expireOrderId, $docType, $expireType){
        $model = new static();
        if ($expireRegulationId == ""){
            $expireRegulationId = NULL;
        }
        if ($expireType == ""){
            $expireType = NULL;
        }
        if ($expireType == ""){
            $expireType = NULL;
        }
        if ($activeRegulationId == ""){
            $activeRegulationId = NULL;
        }
        if ($docType == ""){
            $docType = NULL;
        }
        $model->activeRegulationId = $activeRegulationId;
        $model->expireRegulationId = $expireRegulationId;
        $model->expireOrderId = $expireOrderId;
        $model->docType = $docType;
        $model->expireType = $expireType;
        return $model;
    }
}