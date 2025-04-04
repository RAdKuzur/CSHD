<?php

namespace frontend\events\team;

use frontend\models\work\event\ForeignEventWork;
use common\events\EventInterface;
use common\repositories\team\TeamRepository;
use Yii;

class TeamNameCreateEvent implements EventInterface
{
    private $model;
    private $name;
    private $foreignEventId;
    private TeamRepository $teamRepository;
    public function __construct($model,  $name, $foreignEventId)
    {
        $this->model = $model;
        $this->name = $name;
        $this->foreignEventId = $foreignEventId;
        $this->teamRepository = Yii::createObject(TeamRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute() {
        return
           $this->teamRepository->prepareTeamNameCreate(
               $this->model,
               $this->name,
               $this->foreignEventId
           )
        ;
    }
}