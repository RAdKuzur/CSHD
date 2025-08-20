<?php

namespace backend\services\report\form;

use backend\builders\GroupParticipantReportBuilder;
use backend\builders\ParticipantReportBuilder;
use backend\builders\TrainingGroupReportBuilder;
use backend\forms\report\ManHoursReportForm;
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
    private TrainingGroupReportBuilder $groupBuilder;
    private TrainingGroupRepository $groupRepository;
    private GroupParticipantReportBuilder $participantBuilder;
    private ForeignEventRepository $foreignEventRepository;
    private ActParticipantRepository $actParticipantRepository;
    private SquadParticipantRepository $squadParticipantRepository;
    private ParticipantReportBuilder $eventParticipantBuilder;
    private ParticipantAchievementRepository $participantAchievementRepository;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    public function __construct(
        TrainingGroupReportBuilder $groupBuilder,
        TrainingGroupRepository $groupRepository,
        GroupParticipantReportBuilder $participantBuilder,
        ForeignEventRepository $foreignEventRepository,
        ActParticipantRepository $actParticipantRepository,
        SquadParticipantRepository $squadParticipantRepository,
        ParticipantReportBuilder $eventParticipantBuilder,
        ParticipantAchievementRepository $participantAchievementRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository
    )
    {
        $this->groupBuilder = $groupBuilder;
        $this->groupRepository = $groupRepository;
        $this->participantBuilder = $participantBuilder;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->squadParticipantRepository = $squadParticipantRepository;
        $this->eventParticipantBuilder = $eventParticipantBuilder;
        $this->participantAchievementRepository = $participantAchievementRepository;
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
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
        $data = [];
        foreach (self::EVENT_LEVELS as $level) {
            $events = $this->foreignEventRepository->getByDatesAndLevels(
                $startDate, $endDate,
                [$level]
            );
            $eventParticipants = $this->eventParticipantBuilder->query();
            $eventParticipants = $this->eventParticipantBuilder->filterByEvents($eventParticipants, ArrayHelper::getColumn($events, 'id'));
            $achievementActs = $this->participantAchievementRepository->getByActIds(ArrayHelper::getColumn($eventParticipants->all(), 'id'), [ParticipantAchievementWork::TYPE_WINNER]);
            $actWinners = $this->actParticipantRepository->getByIds(ArrayHelper::getColumn($achievementActs, 'act_participant_id'));
            $winnerParticipants = $this->squadParticipantRepository->getByActIds(ArrayHelper::getColumn($actWinners, 'id'));

            $achievementActs = $this->participantAchievementRepository->getByActIds(ArrayHelper::getColumn($eventParticipants->all(), 'id'), [ParticipantAchievementWork::TYPE_PRIZE]);
            $actPrize = $this->actParticipantRepository->getByIds(ArrayHelper::getColumn($achievementActs, 'act_participant_id'));
            $prizeParticipants = $this->squadParticipantRepository->getByActIds(ArrayHelper::getColumn($actPrize , 'id'));
            $data[$level] = [
                ParticipantAchievementWork::TYPE_PRIZE => count(array_intersect(array_unique(ArrayHelper::getColumn($prizeParticipants, 'participant_id')), ArrayHelper::getColumn($participantsAllUnic->all(), 'participant_id'))),
                ParticipantAchievementWork::TYPE_WINNER => count(array_intersect(array_unique(ArrayHelper::getColumn($winnerParticipants, 'participant_id')),ArrayHelper::getColumn($participantsAllUnic->all(), 'participant_id'))),
            ];
        }
        return [
            'reportData' => [
                'totalCount' => $participantsAllUnic->count(),
                EventLevelDictionary::REGIONAL => $data[EventLevelDictionary::REGIONAL],
                EventLevelDictionary::FEDERAL => $data[EventLevelDictionary::FEDERAL],
                EventLevelDictionary::INTERNATIONAL => $data[EventLevelDictionary::INTERNATIONAL],
            ]
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
        $data = [];
        foreach (self::EVENT_LEVELS as $level) {
            $events = $this->foreignEventRepository->getByDatesAndLevels(
                $startDate, $endDate,
                [$level]
            );
            $eventParticipants = $this->eventParticipantBuilder->query();
            $eventParticipants = $this->eventParticipantBuilder->filterByEvents($eventParticipants, ArrayHelper::getColumn($events, 'id'));
            $achievementActs = $this->participantAchievementRepository->getByActIds(ArrayHelper::getColumn($eventParticipants->all(), 'id'), [ParticipantAchievementWork::TYPE_WINNER, ParticipantAchievementWork::TYPE_PRIZE]);
            $actWinners = $this->actParticipantRepository->getByIds(ArrayHelper::getColumn($achievementActs, 'act_participant_id'));
            $winnerParticipants = $this->squadParticipantRepository->getByActIds(ArrayHelper::getColumn($actWinners, 'id'));
            $data = array_merge($data, $winnerParticipants);
        }
        array_filter($data);
        $unicIds = ArrayHelper::getColumn($participantsAllUnic->all(), 'participant_id');
        $unicIdsMap = array_flip($unicIds);
        $participants = array_filter($data, function($participant) use ($unicIdsMap) {
            return isset($unicIdsMap[$participant->participant_id]);
        });
        return [
            'participants' =>  $participants
        ];

    }
}