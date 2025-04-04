<?php

namespace backend\forms\report;

use common\Model;

class SAForm extends Model
{
    const MAN_HOURS_FAIR = 1;
    const MAN_HOURS_ALL = 2;

    public $startDate;
    public $endDate;
    public $type;

    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'safe'],
            [['type'], 'integer']
        ];
    }
}