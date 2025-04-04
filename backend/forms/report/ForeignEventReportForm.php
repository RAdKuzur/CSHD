<?php

namespace backend\forms\report;

use common\Model;

class ForeignEventReportForm extends Model
{
    public $startDate;
    public $endDate;
    public $branches;
    public $focuses;
    public $allowRemotes;
    public $prizeTypes;
    public $levels;
    public $mode;

    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'string'],
            [['branches', 'focuses', 'allowRemotes', 'prizeTypes', 'levels', 'prizeTypes'], 'safe'],
            [['mode'], 'integer']
        ];
    }
}