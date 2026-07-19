<?php

namespace backend\forms\report;

use common\Model;

class StatsReportForm extends Model
{
    public $year;
    public function rules(){
        return [
            [['year'], 'integer'],
        ];
    }
    public function getYear(){
        return $this->year;
    }
}