<?php

namespace backend\services\report\form;

use backend\builders\GroupParticipantReportBuilder;
use backend\builders\ParticipantReportBuilder;
use backend\builders\TrainingGroupReportBuilder;
use backend\forms\report\ManHoursReportForm;
use backend\services\report\DebugReportService;
use backend\services\report\ReportFacade;
use backend\services\report\ReportForeignEventService;
use common\components\dictionaries\base\AllowRemoteDictionary;
use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\EventLevelDictionary;
use common\components\dictionaries\base\FocusDictionary;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\event\ParticipantAchievementRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\services\act_participant\ActParticipantService;
use Mpdf\Tag\A;
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
        EventLevelDictionary::INTERNATIONAL
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

//        $actsQuery = $this->builder->filterByBranches($actsQuery, [
//            BranchDictionary::TECHNOPARK, BranchDictionary::COD, BranchDictionary::MOBILE_QUANTUM, BranchDictionary::QUANTORIUM, BranchDictionary::CDNTT
//        ]);
//        $actsQuery = $this->builder->filterByFocuses($actsQuery,
//        [
//            FocusDictionary::TECHNICAL, FocusDictionary::ART, FocusDictionary::SOCIAL, FocusDictionary::SCIENCE, FocusDictionary::SPORT
//        ]);
//        $actsQuery = $this->builder->filterByAllowRemote($actsQuery, [
//            AllowRemoteDictionary::ONLY_PERSONAL, AllowRemoteDictionary::PERSONAL_WITH_REMOTE
//        ]);

        $result = [];
        $tempSumPart = 0;
        $tempSumAchieve = 0;
        foreach (self::EVENT_LEVELS as $level) {

            if ($level == EventLevelDictionary::FEDERAL)
            {
                $queryLevels = [EventLevelDictionary::FEDERAL, EventLevelDictionary::INTERREGIONAL];
            }
            else {
                $queryLevels = [$level];
            }

            $participantQuery = $this->builder->filterByEventLevels(clone $actsQuery, $queryLevels);
            $prizeQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_PRIZE]);
            $winQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_WINNER]);

            // Разделяем индивидуальные и командные достижения
            $individualWinners = [];
            $teamWinners = [];
            $individualPrizers = [];
            $teamPrizers = [];

            // Обработка победителей
            foreach ($this->actRepository->findAll($winQuery) as $actParticipant) {
                if (!empty($actParticipant->team_name_id)) {
                    // Командная заявка: сохраняем ID команды
                    $teamWinners[] = $actParticipant->team_name_id;
                } else {
                    // Индивидуальная заявка: сохраняем ID участников
                    foreach ($actParticipant->squadParticipantsWork as $squadParticipant) {
                        $individualWinners[] = $squadParticipant->participant_id;
                    }
                }
            }

            // Обработка призеров (аналогично)
            foreach ($this->actRepository->findAll($prizeQuery) as $actParticipant) {
                if (!empty($actParticipant->team_name_id)) {
                    $teamPrizers[] = $actParticipant->team_name_id;
                } else {
                    foreach ($actParticipant->squadParticipantsWork as $squadParticipant) {
                        $individualPrizers[] = $squadParticipant->participant_id;
                    }
                }
            }

            // Удаляем дубликаты
            $uniqueIndividualWinners = array_unique($individualWinners);
            $uniqueTeamWinners = array_unique($teamWinners);
            $uniqueIndividualPrizers = array_unique($individualPrizers);
            $uniqueTeamPrizers = array_unique($teamPrizers);

            // Считаем итоги: количество людей + количество команд
            $totalWinners = count($uniqueIndividualWinners) + count($uniqueTeamWinners);
            $totalPrizers = count($uniqueIndividualPrizers) + count($uniqueTeamPrizers);


            $result[$level] = [
                'participant' => count($this->actRepository->findAll($participantQuery)),
                'winners' => $totalWinners,
                'prizes' => $totalPrizers
            ];

            // Дополнительно можно сохранить детализацию для отладки
            $result[$level . '_details'] = [
                'individual_winners_count' => count($uniqueIndividualWinners),
                'team_winners_count' => count($uniqueTeamWinners),
                'individual_prizers_count' => count($uniqueIndividualPrizers),
                'team_prizers_count' => count($uniqueTeamPrizers),
            ];

            if (in_array($level, Yii::$app->eventLevel->getReportLevels())) {
                $tempSumPart += count($this->actRepository->findAll($participantQuery));
                $tempSumAchieve += $totalWinners + $totalPrizers;
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

        $events = $this->repository->getByDatesAndLevelsEndDate($startDate, $endDate, self::EVENT_LEVELS);
        $actsQuery = $this->builder->query();
        $actsQuery = $this->builder->filterByEvents($actsQuery, ArrayHelper::getColumn($events, 'id'));
        $actsQuery = $this->builder->joinWith($actsQuery, 'foreignEventWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'actParticipantBranchWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'participantAchievementWork');
        $result = [];
        $tempSumPart = 0;
        $tempSumAchieve = 0;
        foreach (self::EVENT_LEVELS as $level) {
            if ($level == EventLevelDictionary::FEDERAL)
            {
                $queryLevels = [EventLevelDictionary::FEDERAL, EventLevelDictionary::INTERREGIONAL];
            }
            else {
                $queryLevels = [$level];
            }
            $participantQuery = $this->builder->filterByEventLevels(clone $actsQuery, $queryLevels);
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