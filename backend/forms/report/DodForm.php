<?php

namespace backend\forms\report;

use common\Model;

class DodForm extends Model
{
    public $startDate;
    public $endDate;

    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'safe']
        ];
    }
}