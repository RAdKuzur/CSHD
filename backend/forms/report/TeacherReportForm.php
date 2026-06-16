<?php

namespace backend\forms\report;

use common\Model;

class TeacherReportForm extends Model
{
    public $branch;
    public $year;
    public $budget;
    public function rules(){
        return [
            [['branch', 'year', 'budget'], 'required'],
        ];
    }

    public function getBranch(){
        return $this->branch;
    }
    public function getYear(){
        return $this->year;
    }
    public function getBudget(){
        return $this->budget;
    }
}