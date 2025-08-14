<?php

namespace backend\forms\report;

use common\Model;

class ECForm extends Model
{
    public $startDate;
    public $endDate;
    public $budget;
    public function rules(){
        return [
            [['startDate', 'endDate', 'budget'], 'required'],
        ];
    }
}