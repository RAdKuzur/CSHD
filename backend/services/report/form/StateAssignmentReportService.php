<?php

namespace backend\services\report\form;

use backend\builders\GroupParticipantReportBuilder;
use backend\builders\ParticipantReportBuilder;
use backend\builders\TrainingGroupReportBuilder;
use backend\services\report\ReportManHoursService;
use common\components\dictionaries\base\AllowRemoteDictionary;
use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\EventLevelDictionary;
use common\components\dictionaries\base\FocusDictionary;
use common\components\traits\Math;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\event\ForeignEventRepository;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\event\ParticipantAchievementWork;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class StateAssignmentReportService
{
    use Math;

    const PARAM_DUPLICATE = 'duplicate';
    const PARAM_ACHIEVES_RATIO = 'achieves';
    const PARAM_PROJECTS_RATIO = 'projects';
    const PARAM_PARTICIPANTS_RATIO = 'participants';

    private TrainingGroupReportBuilder $groupBuilder;
    private GroupParticipantReportBuilder $participantBuilder;
    private ParticipantReportBuilder $eventParticipantBuilder;
    private TrainingGroupRepository $groupRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private ForeignEventRepository $foreignEventRepository;
    private ActParticipantRepository $actParticipantRepository;

    private ReportManHoursService $manHoursService;

    public function __construct(
        TrainingGroupReportBuilder $groupBuilder,
        GroupParticipantReportBuilder $participantBuilder,
        ParticipantReportBuilder $eventParticipantBuilder,
        TrainingGroupRepository $groupRepository,
        TrainingGroupParticipantRepository $participantRepository,
        ForeignEventRepository $foreignEventRepository,
        ActParticipantRepository $actParticipantRepository,
        ReportManHoursService $manHoursService
    )
    {
        $this->groupBuilder = $groupBuilder;
        $this->participantBuilder = $participantBuilder;
        $this->eventParticipantBuilder = $eventParticipantBuilder;
        $this->groupRepository = $groupRepository;
        $this->participantRepository = $participantRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->manHoursService = $manHoursService;
    }

    /**
     * Заполнение раздела 3.1 гос задания
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function fillSection31(string $startDate, string $endDate)
    {
        $result = [];

        $result['technopark']['tech']['personal'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::TECHNOPARK,
                FocusDictionary::TECHNICAL,
                AllowRemoteDictionary::ONLY_PERSONAL,
                [self::PARAM_DUPLICATE, self::PARAM_PROJECTS_RATIO, self::PARAM_ACHIEVES_RATIO]
            );

        $result['cdntt']['tech']['personal'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::CDNTT,
                FocusDictionary::TECHNICAL,
                AllowRemoteDictionary::ONLY_PERSONAL,
                [self::PARAM_DUPLICATE, self::PARAM_ACHIEVES_RATIO]
            );

        $result['cdntt']['art']['personal'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::CDNTT,
                FocusDictionary::ART,
                AllowRemoteDictionary::ONLY_PERSONAL,
                [self::PARAM_DUPLICATE, self::PARAM_ACHIEVES_RATIO]
            );

        $result['cdntt']['social']['personal'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::CDNTT,
                FocusDictionary::SOCIAL,
                AllowRemoteDictionary::ONLY_PERSONAL,
                [self::PARAM_DUPLICATE, self::PARAM_ACHIEVES_RATIO]
            );

        $result['quantorium']['tech']['personal'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::QUANTORIUM,
                FocusDictionary::TECHNICAL,
                AllowRemoteDictionary::ONLY_PERSONAL,
                [self::PARAM_DUPLICATE, self::PARAM_PROJECTS_RATIO, self::PARAM_ACHIEVES_RATIO]
            );

        $result['mob_quant']['tech']['personal'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::MOBILE_QUANTUM,
                FocusDictionary::TECHNICAL,
                AllowRemoteDictionary::ONLY_PERSONAL,
                [self::PARAM_PROJECTS_RATIO]
            );

        $result['cod']['tech']['personal'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::COD,
                FocusDictionary::TECHNICAL,
                AllowRemoteDictionary::ONLY_PERSONAL,
                [self::PARAM_DUPLICATE, self::PARAM_PROJECTS_RATIO, self::PARAM_ACHIEVES_RATIO]
            );

        $result['cod']['tech']['remote'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::COD,
                FocusDictionary::TECHNICAL,
                AllowRemoteDictionary::PERSONAL_WITH_REMOTE,
                [self::PARAM_PARTICIPANTS_RATIO]
            );

        $result['cod']['science']['personal'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::COD,
                FocusDictionary::SCIENCE,
                AllowRemoteDictionary::ONLY_PERSONAL,
                [self::PARAM_DUPLICATE, self::PARAM_PROJECTS_RATIO, self::PARAM_ACHIEVES_RATIO]
            );

        $result['cod']['art']['personal'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::COD,
                FocusDictionary::ART,
                AllowRemoteDictionary::ONLY_PERSONAL,
                [self::PARAM_DUPLICATE, self::PARAM_PROJECTS_RATIO]
            );

        $result['cod']['sport']['personal'] =
            $this->calculateParamsSection31(
                $startDate, $endDate,
                BranchDictionary::COD,
                FocusDictionary::SPORT,
                AllowRemoteDictionary::ONLY_PERSONAL,
                [self::PARAM_DUPLICATE, self::PARAM_ACHIEVES_RATIO]
            );

        return $result;
    }

    /**
     * Заполнение раздела 3.2 гос. задания
     * Вся бизнес-логика находится в {@see ReportManHoursService}, который используется в отчетах по запросу
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $type
     * @return array
     */
    public function fillSection32(string $startDate, string $endDate, int $type)
    {
        $result = [];

        $result['technopark']['tech']['personal'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::TECHNOPARK],
                [FocusDictionary::TECHNICAL],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        $result['cdntt']['tech']['personal'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::CDNTT],
                [FocusDictionary::TECHNICAL],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        $result['cdntt']['art']['personal'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::CDNTT],
                [FocusDictionary::ART],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        $result['cdntt']['social']['personal'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::CDNTT],
                [FocusDictionary::SOCIAL],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        $result['quantorium']['tech']['personal'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::QUANTORIUM],
                [FocusDictionary::TECHNICAL],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        $result['mob_quant']['tech']['personal'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::MOBILE_QUANTUM],
                [FocusDictionary::TECHNICAL],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        $result['cod']['tech']['personal'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::COD],
                [FocusDictionary::TECHNICAL],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        $result['cod']['tech']['remote'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::COD],
                [FocusDictionary::TECHNICAL],
                [AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        $result['cod']['science']['personal'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::COD],
                [FocusDictionary::SCIENCE],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        $result['cod']['art']['personal'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::COD],
                [FocusDictionary::ART],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        $result['cod']['sport']['personal'] =
            $this->manHoursService->calculateManHours(
                $startDate, $endDate,
                [BranchDictionary::COD],
                [FocusDictionary::SPORT],
                [AllowRemoteDictionary::ONLY_PERSONAL],
                [TrainingGroupWork::IS_BUDGET],
                $type
            )['result'];

        return $result;
    }

    /**
     * Основной метод подсчета показателей для раздела 3.1
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $branch
     * @param int $focus
     * @param int $allowRemote
     * @param array $params
     * @return array
     */
    public function calculateParamsSection31(string $startDate, string $endDate, int $branch, int $focus, int $allowRemote, array $params = [])
    {
        $groupsQuery = $this->groupBuilder->query();
        $groupsQuery = $this->groupBuilder->filterGroupsByDates($groupsQuery, $startDate, $endDate);
        $groupsQuery = $this->groupBuilder->filterGroupsByBranches($groupsQuery, [$branch]);
        $groupsQuery = $this->groupBuilder->filterGroupsByFocuses($groupsQuery, [$focus]);
        $groupsQuery = $this->groupBuilder->filterGroupsByAllowRemote($groupsQuery, [$allowRemote]);
        $groupsQuery = $this->groupBuilder->filterGroupsByBudget($groupsQuery, [TrainingGroupWork::IS_BUDGET]);
        $groupsAll = $this->groupRepository->findAll($groupsQuery);

        $eventsAll = $this->foreignEventRepository->getByDatesAndLevels(
            $startDate, $endDate,
            [EventLevelDictionary::REGIONAL, EventLevelDictionary::FEDERAL, EventLevelDictionary::INTERNATIONAL]
        );

        return [
            self::PARAM_DUPLICATE => in_array(self::PARAM_DUPLICATE, $params) ? $this->calculateParamDuplicate($groupsAll) : -1,
            self::PARAM_ACHIEVES_RATIO => in_array(self::PARAM_ACHIEVES_RATIO, $params) ? $this->calculateParamRatioAchieves($groupsAll, $eventsAll) : -1,
            self::PARAM_PROJECTS_RATIO => in_array(self::PARAM_PROJECTS_RATIO, $params) ? $this->calculateParamRatioProjects($groupsAll) : -1,
            self::PARAM_PARTICIPANTS_RATIO => in_array(self::PARAM_PARTICIPANTS_RATIO, $params) ? $this->calculateParamRatioParticipants($groupsAll, $eventsAll) : -1,
        ];
    }

    /**
     * Метод подсчета доли лиц, подавших более одного заявление на обучение от общего числа
     * Считаем количество людей, которые занимаются более чем в одной группе по соответствующим параметрам и делим на все уникальные акты обучения
     *
     * @param array $groups
     * @return float
     */
    public function calculateParamDuplicate(array $groups)
    {
        $participants = $this->participantBuilder->query();
        $participants = $this->participantBuilder->filterByGroups($participants, ArrayHelper::getColumn($groups, 'id'));
        $participantsDuplicate = $this->participantBuilder->groupByWithHaving(clone $participants, 'participant_id', 'COUNT(`training_group_id`) > 1');

        $participantsAllUnic = $this->participantBuilder->distinct(clone $participants, ['participant_id']);

        return $this->percent($this->participantRepository->count($participantsDuplicate), $this->participantRepository->count($participantsAllUnic));
    }

    /**
     * Метод подсчета доли победителей и призеров к общему числу участников
     * Считаем только участников, обучавшихся в соответствующих группах
     * Уровень мероприятия - региональный и выше
     *
     * @param array $groups
     * @param array $events
     * @return float
     */
    public function calculateParamRatioAchieves(array $groups, array $events)
    {
        $participants = $this->participantBuilder->query();
        $participants = $this->participantBuilder->filterByGroups($participants, ArrayHelper::getColumn($groups, 'id'));
        $participantsAll = $this->participantRepository->findAll($participants);

        $eventParticipants = $this->eventParticipantBuilder->query();
        $eventParticipants = $this->eventParticipantBuilder->joinWith($eventParticipants, 'foreignEventWork');
        $eventParticipants = $this->eventParticipantBuilder->joinWith($eventParticipants, 'squadParticipantWork');
        $eventParticipants = $this->eventParticipantBuilder->joinWith($eventParticipants, 'participantAchievementWork');
        $eventParticipants = $this->eventParticipantBuilder->filterByEvents($eventParticipants, ArrayHelper::getColumn($events, 'id'));
        $eventParticipants = $this->eventParticipantBuilder->filterByEventLevels(
            $eventParticipants,
            [EventLevelDictionary::REGIONAL, EventLevelDictionary::FEDERAL, EventLevelDictionary::INTERNATIONAL]
        );
        $eventParticipants = $this->eventParticipantBuilder->filterByParticipantIds($eventParticipants, ArrayHelper::getColumn($participantsAll, 'participant_id'));

        $eventParticipantsAchieves = $this->eventParticipantBuilder->filterByPrizes(clone $eventParticipants, [ParticipantAchievementWork::TYPE_PRIZE, ParticipantAchievementWork::TYPE_WINNER]);

        return $this->percent($this->actParticipantRepository->count($eventParticipantsAchieves), $this->actParticipantRepository->count($eventParticipants));
    }

    /**
     * Метод подсчета доли учеников, защитивших проект к общему числу учеников
     * Защитившим проект считается ученик, у которого поле group_project_themes_id != null {@see TrainingGroupParticipantWork}
     *
     * @param array $groups
     * @return float
     */
    public function calculateParamRatioProjects(array $groups)
    {
        $participants = $this->participantBuilder->query();
        $participants = $this->participantBuilder->filterByGroups($participants, ArrayHelper::getColumn($groups, 'id'));

        $participantsProject = $this->participantBuilder->filterByProjectThemes(clone $participants, 1);

        return $this->percent($this->participantRepository->count($participantsProject), $this->participantRepository->count($participants));
    }

    /**
     * Метод подсчета доли участников мероприятий к общему числу обучающихся
     * Уровень мероприятия - региональный и выше
     *
     * @param array $groups
     * @param array $events
     * @return float
     */
    public function calculateParamRatioParticipants(array $groups, array $events)
    {
        $participants = $this->participantBuilder->query();
        $participants = $this->participantBuilder->filterByGroups($participants, ArrayHelper::getColumn($groups, 'id'));
        $participantsAll = $this->participantRepository->findAll($participants);

        $eventParticipants = $this->eventParticipantBuilder->query();
        $eventParticipants = $this->eventParticipantBuilder->joinWith($eventParticipants, 'squadParticipantWork');
        $eventParticipants = $this->eventParticipantBuilder->joinWith($eventParticipants, 'foreignEventWork');
        $eventParticipants = $this->eventParticipantBuilder->filterByEvents($eventParticipants, ArrayHelper::getColumn($events, 'id'));
        $eventParticipants = $this->eventParticipantBuilder->filterByEventLevels(
            $eventParticipants,
            [EventLevelDictionary::REGIONAL, EventLevelDictionary::FEDERAL, EventLevelDictionary::INTERNATIONAL]
        );
        $eventParticipants = $this->eventParticipantBuilder->filterByParticipantIds($eventParticipants, ArrayHelper::getColumn($participantsAll, 'participant_id'));

        return $this->percent($this->actParticipantRepository->count($eventParticipants), count($participantsAll));
    }
}