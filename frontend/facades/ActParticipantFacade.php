<?php

namespace frontend\facades;

use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use frontend\services\act_participant\ActParticipantService;
use frontend\services\team\TeamService;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\team\TeamRepository;
use yii\helpers\ArrayHelper;

class ActParticipantFacade
{
    private ActParticipantService $actParticipantService;
    private ActParticipantRepository $actParticipantRepository;
    private TeamService $teamService;
    private TeamRepository $teamRepository;
    private PeopleRepository $peopleRepository;
    private ForeignEventParticipantsRepository $foreignEventParticipantsRepository;
    public function __construct(
        ActParticipantService $actParticipantService,
        ActParticipantRepository $actParticipantRepository,
        TeamService $teamService,
        TeamRepository $teamRepository,
        PeopleRepository $peopleRepository,
        ForeignEventParticipantsRepository $foreignEventParticipantsRepository
    )
    {
        $this->actParticipantService = $actParticipantService;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->teamService = $teamService;
        $this->teamRepository = $teamRepository;
        $this->peopleRepository = $peopleRepository;
        $this->foreignEventParticipantsRepository = $foreignEventParticipantsRepository;
    }

    public function prepareActFacade($act){
        $modelAct = $this->actParticipantService->createForms($act);
        $people = $this->peopleRepository->getOrderedList();
        $participants = $this->foreignEventParticipantsRepository->getAll();
        $nominations = array_unique(ArrayHelper::getColumn($this->actParticipantRepository->getByForeignEventIds([$act[0]->foreign_event_id]), 'nomination'));
        $teams = $this->teamService->getNamesByForeignEventId($act[0]->foreign_event_id);
        $defaultTeam = $this->teamRepository->getById($act[0]->team_name_id);
        $tables = $this->actParticipantService->createActFileTable($act[0]);
        return [
            'modelAct' => $modelAct,
            'people' => $people,
            'nominations' => $nominations,
            'teams' => $teams,
            'defaultTeam' => $defaultTeam,
            'tables' => $tables,
            'participants' => $participants,
        ];
    }
}