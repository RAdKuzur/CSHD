<?php

namespace backend\services\report\form;

use backend\builders\GroupParticipantReportBuilder;
use backend\builders\ParticipantReportBuilder;
use backend\builders\TrainingGroupReportBuilder;
use backend\forms\report\ManHoursReportForm;
use backend\services\report\DebugReportService;
use backend\services\report\ReportFacade;
use backend\services\report\ReportForeignEventService;
use common\components\dictionaries\base\EventLevelDictionary;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\event\ParticipantAchievementRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\services\act_participant\ActParticipantService;
use Yii;
use yii\helpers\ArrayHelper;

class EffectiveContractReportService
{
    const CALCULATE_TYPES = [
        ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN,
        ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER,
        ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN,
        ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER
    ];
    const EVENT_LEVELS = [
        EventLevelDictionary::REGIONAL,
        EventLevelDictionary::FEDERAL,
        EventLevelDictionary::INTERNATIONAL,
    ];

    private ForeignEventRepository $repository;
    private ParticipantReportBuilder $builder;
    private ActParticipantRepository $actRepository;
    private TrainingGroupReportBuilder $groupBuilder;
    private TrainingGroupRepository $groupRepository;
    private GroupParticipantReportBuilder $participantBuilder;

    public function __construct(
        ForeignEventRepository   $repository,
        ParticipantReportBuilder $builder,
        ActParticipantRepository $actRepository,
        TrainingGroupReportBuilder $groupBuilder,
        TrainingGroupRepository $groupRepository,
        GroupParticipantReportBuilder $participantBuilder
    )
    {
        $this->repository = $repository;
        $this->builder = $builder;
        $this->actRepository = $actRepository;
        $this->groupBuilder = $groupBuilder;
        $this->groupRepository = $groupRepository;
        $this->participantBuilder = $participantBuilder;
    }
    public function fillDodSection($startDate, $endDate, $type)
    {
        $groupsQuery = $this->groupBuilder->query();
        $groupsQuery = $this->groupBuilder->filterGroupsByDates($groupsQuery, $startDate, $endDate, self::CALCULATE_TYPES);
        $groupsQuery = $this->groupBuilder->filterGroupsByBudget($groupsQuery, $type);
        $groups = $this->groupRepository->findAll($groupsQuery);

        $participants = $this->participantBuilder->query();
        $participants = $this->participantBuilder->filterByGroups($participants, ArrayHelper::getColumn($groups, 'id'));
        $participantsAllUnic = $this->participantBuilder->distinct(clone $participants, ['participant_id']);


        $events = $this->repository->getByDatesAndLevels($startDate, $endDate, self::EVENT_LEVELS);

        $actsQuery = $this->builder->query();
        $actsQuery = $this->builder->filterByEvents($actsQuery, ArrayHelper::getColumn($events, 'id'));
        $actsQuery = $this->builder->joinWith($actsQuery, 'foreignEventWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'actParticipantBranchWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'participantAchievementWork');
        $result = [];
        $tempSumPart = 0;
        $tempSumAchieve = 0;
        foreach (self::EVENT_LEVELS as $level) {
            $participantQuery = $this->builder->filterByEventLevels(clone $actsQuery, [$level]);
            $prizeQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_PRIZE]);
            $winQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_WINNER]);
            $result[$level] = [
                'participant' => count($this->actRepository->findAll($participantQuery)),
                'winners' => count($this->actRepository->findAll($winQuery)),
                'prizes' => count($this->actRepository->findAll($prizeQuery)),
            ];

            if (in_array($level, Yii::$app->eventLevel->getReportLevels())) {
                $tempSumPart += count($this->actRepository->findAll($participantQuery));
                $tempSumAchieve +=
                    count($this->actRepository->findAll($winQuery)) +
                    count($this->actRepository->findAll($prizeQuery));
            }
        }
        $result['percent'] = $tempSumPart != 0 ? round($tempSumAchieve / $tempSumPart, 2) : 0;
        return [
            'result' => $result,
            'totalCount' => count($participantsAllUnic->all()),
        ];
    }
    public function fillParticipantSection($startDate, $endDate, $type){
        $groupsQuery = $this->groupBuilder->query();
        $groupsQuery = $this->groupBuilder->filterGroupsByDates($groupsQuery, $startDate, $endDate, self::CALCULATE_TYPES);
        $groupsQuery = $this->groupBuilder->filterGroupsByBudget($groupsQuery, $type);
        $groups = $this->groupRepository->findAll($groupsQuery);

        $participants = $this->participantBuilder->query();
        $participants = $this->participantBuilder->filterByGroups($participants, ArrayHelper::getColumn($groups, 'id'));
        $participantsAllUnic = $this->participantBuilder->distinct(clone $participants, ['participant_id']);

        $events = $this->repository->getByDatesAndLevels($startDate, $endDate, self::EVENT_LEVELS);
        $actsQuery = $this->builder->query();
        $actsQuery = $this->builder->filterByEvents($actsQuery, ArrayHelper::getColumn($events, 'id'));
        $actsQuery = $this->builder->joinWith($actsQuery, 'foreignEventWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'actParticipantBranchWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'participantAchievementWork');
        $result = [];
        $tempSumPart = 0;
        $tempSumAchieve = 0;
        foreach (self::EVENT_LEVELS as $level) {
            $participantQuery = $this->builder->filterByEventLevels(clone $actsQuery, [$level]);
            $prizeQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_PRIZE]);
            $winQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_WINNER]);
            $result['levels'][$level] = [
                'participantsWinner' => $this->actRepository->findAll($winQuery),
                'participantsPrize' => $this->actRepository->findAll($prizeQuery),
            ];

            if (in_array($level, Yii::$app->eventLevel->getReportLevels())) {
                $tempSumPart += count($this->actRepository->findAll($participantQuery));
                $tempSumAchieve +=
                    count($this->actRepository->findAll($winQuery)) +
                    count($this->actRepository->findAll($prizeQuery));
            }
        }
        $result['percent'] = $tempSumPart != 0 ? round($tempSumAchieve / $tempSumPart, 2) : 0;
        return [
            'result' => $result,
        ];
    }
}