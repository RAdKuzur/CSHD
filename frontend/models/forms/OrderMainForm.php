<?php

namespace app\models\forms;

use common\Model;
use frontend\controllers\order\OrderMainController;
use frontend\models\work\order\OrderMainWork;


class OrderMainForm extends Model
{
    public $entity;
    public $people;
    public $orders;
    public $regulations;
    public $modelExpire;
    public $modelChangedDocuments;
    public $tables;

    public function __construct(
        $entity,
        $people,
        $orders,
        $regulations,
        $modelExpire,
        $modelChangedDocuments,
        $tables,
        array $config = []
    )
    {
        parent::__construct($config);
        $this->entity = $entity;
        $this->people = $people;
        $this->orders = $orders;
        $this->regulations = $regulations;
        $this->modelExpire = $modelExpire;
        $this->modelChangedDocuments = $modelChangedDocuments;
        $this->tables = $tables;
    }
}