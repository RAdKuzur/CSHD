<?php

namespace backend\forms\report;

use common\Model;

class TeacherReportForm extends Model
{
    public $branch;
    public $year;
    public function rules(){
        return [
            [['branch', 'year'], 'integer'],
        ];
    }

    public function getBranch(){
        return $this->branch;
    }
    public function getYear(){
        return $this->year;
    }
}