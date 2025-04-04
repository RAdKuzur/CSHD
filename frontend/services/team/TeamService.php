<?php

namespace frontend\services\team;

use frontend\events\team\TeamNameCreateEvent;
use frontend\models\work\team\TeamNameWork;
use common\repositories\team\TeamRepository;
use yii\helpers\ArrayHelper;

class TeamService
{
    private TeamRepository $teamRepository;
    public function __construct(
        TeamRepository $teamRepository
    )
    {
       $this->teamRepository = $teamRepository;
    }
    public function teamNameCreateEvent($foreignEventId, $name){
        if(!$this->teamRepository->getByNameAndForeignEventId($foreignEventId, $name)){
            $model = new TeamNameWork();
            if($name != NULL && $name != "NULL") {
                $model->recordEvent(new TeamNameCreateEvent($model, $name, $foreignEventId), TeamNameWork::class);
                $model->releaseEvents();
            }
        }
        else {
            $model = $this->teamRepository->getByNameAndForeignEventId($foreignEventId, $name);
        }
        return $model->id;
    }
    public function getNamesByForeignEventId($foreignEventId){
        $teams = $this->teamRepository->getNamesByForeignEventId($foreignEventId);
        return ArrayHelper::getColumn($teams, 'name');
    }
}