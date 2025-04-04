<?php

namespace app\models\forms;

use common\Model;

class OrderTrainingForm extends Model
{
    public $people;
    public $groups;
    public $groupParticipant;
    public $transferGroups;
    public $tables;
    public $groupCheckOption;
    public $groupParticipantOption;
    public function __construct(
        $people,
        $groups,
        $groupParticipant,
        $transferGroups,
        $tables,
        $groupCheckOption,
        $groupParticipantOption,
        array $config = []
    )
    {
        $this->people = $people;
        $this->groups = $groups;
        $this->groupParticipant = $groupParticipant;
        $this->transferGroups = $transferGroups;
        $this->tables = $tables;
        $this->groupCheckOption = $groupCheckOption;
        $this->groupParticipantOption = $groupParticipantOption;
        parent::__construct($config);
    }
}