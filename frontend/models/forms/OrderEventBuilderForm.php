<?php

namespace app\models\forms;

use common\Model;

class OrderEventBuilderForm extends Model
{
    public $orderEventForm;
    public $people;
    public $modelActs;
    public $teams;
    public $nominations;
    public $participants;
    public $company;
    public $actTable;
    public $tables;

    public function __construct(
        $orderEventForm,
        $people,
        $modelActs,
        $teams,
        $nominations,
        $participants,
        $company,
        $actTable,
        $tables,
        $config = [])
    {
        $this->orderEventForm = $orderEventForm;
        $this->people = $people;
        $this->modelActs = $modelActs;
        $this->teams = $teams;
        $this->nominations = $nominations;
        $this->participants = $participants;
        $this->company = $company;
        $this->actTable = $actTable;
        $this->tables = $tables;
        parent::__construct($config);
    }
}