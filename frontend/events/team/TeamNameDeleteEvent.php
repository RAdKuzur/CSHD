<?php

namespace app\events\team;

use common\events\EventInterface;
use common\repositories\team\TeamRepository;
use Yii;

class TeamNameDeleteEvent implements EventInterface
{
    private $id;
    private TeamRepository $teamRepository;
    public function __construct($id)
    {
        $this->id = $id;
        $this->teamRepository = Yii::createObject(TeamRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute() {
        return
            [
                $this->teamRepository->prepareTeamNameDelete($this->id)
            ];
    }
}