<?php

namespace backend\services\report\form;

use backend\builders\AuditoriumReportBuilder;
use backend\builders\GroupParticipantReportBuilder;
use backend\builders\TrainingGroupReportBuilder;
use common\components\dictionaries\base\AllowRemoteDictionary;
use common\components\dictionaries\base\AuditoriumTypeDictionary;
use common\components\dictionaries\base\FocusDictionary;
use common\repositories\dictionaries\AuditoriumRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\helpers\ArrayHelper;

class DodReportService
{
    private TrainingGroupRepository $groupRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private AuditoriumRepository $auditoriumRepository;
    private TrainingGroupReportBuilder $groupBuilder;
    private GroupParticipantReportBuilder $participantBuilder;
    private AuditoriumReportBuilder $auditoriumBuilder;

    public function __construct(
        TrainingGroupRepository $groupRepository,
        TrainingGroupParticipantRepository $participantRepository,
        AuditoriumRepository $auditoriumRepository,
        TrainingGroupReportBuilder $groupBuilder,
        GroupParticipantReportBuilder $participantBuilder,
        AuditoriumReportBuilder $auditoriumBuilder
    )
    {
        $this->groupRepository = $groupRepository;
        $this->participantRepository = $participantRepository;
        $this->auditoriumRepository = $auditoriumRepository;
        $this->groupBuilder = $groupBuilder;
        $this->participantBuilder = $participantBuilder;
        $this->auditoriumBuilder = $auditoriumBuilder;
    }

    /**
     * Функция-фасад для подсчета всех данных для Раздела 3 ДОД
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function fillSection3(string $startDate, string $endDate) : array
    {
        $result = [];
        // Предварительная подготовка общей части запроса для получения списка групп
        $groupQueries = $this->createGroupQuery($startDate, $endDate);

        $result['tech'] = $this->calculateParticipantsSection3($groupQueries, FocusDictionary::TECHNICAL,$endDate);
        $result['science'] = $this->calculateParticipantsSection3($groupQueries, FocusDictionary::SCIENCE,$endDate);
        $result['social'] = $this->calculateParticipantsSection3($groupQueries, FocusDictionary::SOCIAL,$endDate);
        $result['art'] = $this->calculateParticipantsSection3($groupQueries, FocusDictionary::ART,$endDate);
        $result['sport'] = $this->calculateParticipantsSection3($groupQueries, FocusDictionary::SPORT,$endDate);

        return $result;
    }

    /**
     * Функция-фасад для подсчета всех данных для Раздела 4 ДОД
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function fillSection4(string $startDate, string $endDate) : array
    {
        $result = [];
        $groupQueries = $this->createGroupQuery($startDate, $endDate);

        // Передаем endDate в каждый вызов
        $result['tech'] = $this->calculateParticipantsSection4($groupQueries, FocusDictionary::TECHNICAL, $endDate);
        $result['science'] = $this->calculateParticipantsSection4($groupQueries, FocusDictionary::SCIENCE, $endDate);
        $result['social'] = $this->calculateParticipantsSection4($groupQueries, FocusDictionary::SOCIAL, $endDate);
        $result['art'] = $this->calculateParticipantsSection4($groupQueries, FocusDictionary::ART, $endDate);
        $result['sport'] = $this->calculateParticipantsSection4($groupQueries, FocusDictionary::SPORT, $endDate);

        $groupQueries = $this->createGroupQuery($startDate, $endDate);
        $result['summary'] = $this->calculateParticipantsSection4($groupQueries, -1, $endDate);

        return $result;
    }

    /**
     * Функция-фасад для подсчета всех данных для Раздела 5 ДОД
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function fillSection5(string $startDate, string $endDate) : array
    {
        $result = [];
        // Предварительная подготовка общей части запроса для получения списка групп
        $groupQueries = $this->createGroupQuery($startDate, $endDate);

        $result['tech'] = $this->calculateParticipantsSection5($groupQueries, FocusDictionary::TECHNICAL,$endDate);
        $result['science'] = $this->calculateParticipantsSection5($groupQueries, FocusDictionary::SCIENCE,$endDate);
        $result['social'] = $this->calculateParticipantsSection5($groupQueries, FocusDictionary::SOCIAL,$endDate);
        $result['art'] = $this->calculateParticipantsSection5($groupQueries, FocusDictionary::ART,$endDate);
        $result['sport'] = $this->calculateParticipantsSection5($groupQueries, FocusDictionary::SPORT,$endDate);

        return $result;
    }

    /**
     * Функция-фасад для подсчета всех данных для Раздела 10 ДОД
     *
     * @return array
     */
    public function fillSection10() : array
    {
        $auditoriumsOwner = $this->auditoriumBuilder->query();
        $auditoriumsRent = $this->auditoriumBuilder->filterByOwnership(clone $auditoriumsOwner);

        $labsOwner = $this->auditoriumBuilder->filterByType(clone $auditoriumsOwner, [AuditoriumTypeDictionary::LABORATORY]);
        $labsRent = $this->auditoriumBuilder->filterByType(clone $auditoriumsRent, [AuditoriumTypeDictionary::LABORATORY]);

        $workOwner = $this->auditoriumBuilder->filterByType(clone $auditoriumsOwner, [AuditoriumTypeDictionary::WORKSHOP]);
        $workRent = $this->auditoriumBuilder->filterByType(clone $auditoriumsRent, [AuditoriumTypeDictionary::WORKSHOP]);

        $studyOwner = $this->auditoriumBuilder->filterByType(clone $auditoriumsOwner, [AuditoriumTypeDictionary::STUDY_CLASS]);
        $studyRent = $this->auditoriumBuilder->filterByType(clone $auditoriumsRent, [AuditoriumTypeDictionary::STUDY_CLASS]);

        $lectOwner = $this->auditoriumBuilder->filterByType(clone $auditoriumsOwner, [AuditoriumTypeDictionary::LECTURE_HALL]);
        $lectRent = $this->auditoriumBuilder->filterByType(clone $auditoriumsRent, [AuditoriumTypeDictionary::LECTURE_HALL]);

        $compOwner = $this->auditoriumBuilder->filterByType(clone $auditoriumsOwner, [AuditoriumTypeDictionary::COMPUTER_ROOM]);
        $compRent = $this->auditoriumBuilder->filterByType(clone $auditoriumsRent, [AuditoriumTypeDictionary::COMPUTER_ROOM]);

        $hallOwner = $this->auditoriumBuilder->filterByType(clone $auditoriumsOwner, [AuditoriumTypeDictionary::ASSEMBLY_HALL]);
        $hallRent = $this->auditoriumBuilder->filterByType(clone $auditoriumsRent, [AuditoriumTypeDictionary::ASSEMBLY_HALL]);

        return [
            'laboratory' => [
                'owner' => $this->auditoriumRepository->findOne($labsOwner) ? 1 : 2,
                'rent' => $this->auditoriumRepository->findOne($labsRent) ? 1 : 2
            ],
            'workshop' => [
                'owner' => $this->auditoriumRepository->findOne($workOwner) ? 1 : 2,
                'rent' => $this->auditoriumRepository->findOne($workRent) ? 1 : 2
            ],
            'study' => [
                'owner' => $this->auditoriumRepository->findOne($studyOwner) ? 1 : 2,
                'rent' => $this->auditoriumRepository->findOne($studyRent) ? 1 : 2
            ],
            'lecture' => [
                'owner' => $this->auditoriumRepository->findOne($lectOwner) ? 1 : 2,
                'rent' => $this->auditoriumRepository->findOne($lectRent) ? 1 : 2
            ],
            'computer' => [
                'owner' => $this->auditoriumRepository->findOne($compOwner) ? 1 : 2,
                'rent' => $this->auditoriumRepository->findOne($compRent) ? 1 : 2
            ],
            'hall' => [
                'owner' => $this->auditoriumRepository->findOne($hallOwner) ? 1 : 2,
                'rent' => $this->auditoriumRepository->findOne($hallRent) ? 1 : 2
            ],
        ];
    }

    /**
     * Функция-фасад для подсчета всех данных для Раздела 11 ДОД
     *
     * @return array
     */
    public function fillSection11() : array
    {
        $auditoriums = $this->auditoriumBuilder->query();
        $auditoriums = $this->auditoriumBuilder->filterByIncludeSquare($auditoriums, [AuditoriumWork::IS_INCLUDE]);

        $allSquare = array_sum(
            ArrayHelper::getColumn(
                $this->auditoriumRepository->findAll($auditoriums),
                'square'
            )
        );

        $educationAuditoriums = $this->auditoriumBuilder->filterByEducation(clone $auditoriums, [AuditoriumWork::IS_EDUCATION]);
        $educationSquare = array_sum(
            ArrayHelper::getColumn(
                $this->auditoriumRepository->findAll($educationAuditoriums),
                'square'
            )
        );

        $allOwnerAuditoriums = $this->auditoriumBuilder->filterByOwnership(clone $auditoriums);
        $allOwnerSquare = array_sum(
            ArrayHelper::getColumn(
                $this->auditoriumRepository->findAll($allOwnerAuditoriums),
                'square'
            )
        );

        $educationalOwnerAuditoriums = $this->auditoriumBuilder->filterByOwnership(clone $educationAuditoriums);
        $educationalOwnerSquare = array_sum(
            ArrayHelper::getColumn(
                $this->auditoriumRepository->findAll($educationalOwnerAuditoriums),
                'square'
            )
        );

        $allRentAuditoriums = $this->auditoriumBuilder->filterByRent(clone $auditoriums);
        $allRentSquare = array_sum(
            ArrayHelper::getColumn(
                $this->auditoriumRepository->findAll($allRentAuditoriums),
                'square'
            )
        );

        $educationalRentAuditoriums = $this->auditoriumBuilder->filterByRent(clone $educationAuditoriums);
        $educationalRentSquare = array_sum(
            ArrayHelper::getColumn(
                $this->auditoriumRepository->findAll($educationalRentAuditoriums),
                'square'
            )
        );


        return [
            'all' => $allSquare,
            'educational' => $educationSquare,
            'all_owner' => $allOwnerSquare,
            'educational_owner' => $educationalOwnerSquare,
            'all_rent' => $allRentSquare,
            'educational_rent' => $educationalRentSquare
        ];
    }


    /**
     * Основной метод расчета количества обучающихся в Разделе 3
     *
     * @param array $groupQuery
     * @param int $focus
     * @return array
     */
    public function calculateParticipantsSection3(array $groupQuery, int $focus,string $endDate) : array
    {
        $queryAll = $this->groupBuilder->filterGroupsByFocuses(clone $groupQuery['all'], [$focus]); // все группы
        $temp = $queryAll->createCommand()->getRawSql();
        $queryRemote = $this->groupBuilder->filterGroupsByFocuses(clone $groupQuery['remote'], [$focus]); // только с дистантом
        $queryNetwork = $this->groupBuilder->filterGroupsByFocuses(clone $groupQuery['network'], [$focus]); // только сетевые

        $groupsAll = $this->groupRepository->findAll($queryAll);
        $groupsRemote = $this->groupRepository->findAll($queryRemote);
        $groupsNetwork = $this->groupRepository->findAll($queryNetwork);

        $participantsQueries = $this->createParticipantQuery($groupsAll, $endDate); // готовые запросы по полу обучающихся (все группы)
        $participantsRemoteQueries = $this->createParticipantQuery($groupsRemote, $endDate); // готовые запросы по полу обучающихся (дистант группы)
        $participantsNetworkQueries = $this->createParticipantQuery($groupsNetwork, $endDate); // готовые запросы по полу обучающихся (сетевые группы)
        $participantsAll = $this->participantRepository->findAll($participantsQueries['all']);// все обучающиеся со всех групп
        $participantsFemale = $this->participantRepository->findAll($participantsQueries['female']); // только девочки со всех групп
        $participantsNetworkAll = $this->participantRepository->findAll($participantsNetworkQueries['all']); // все обучающиеся из сетевых групп
        $participantsRemoteAll = $this->participantRepository->findAll($participantsRemoteQueries['all']); // все обучающиеся с дистант групп

        return [
            'all' => count(array_unique(ArrayHelper::getColumn($participantsAll, 'participant_id'))),
            'female' => count(array_unique(ArrayHelper::getColumn($participantsFemale, 'participant_id'))),
            'network' => count(array_unique(ArrayHelper::getColumn($participantsNetworkAll, 'participant_id'))),
            'remote' => count(array_unique(ArrayHelper::getColumn($participantsRemoteAll, 'participant_id'))),
        ];
    }

    /**
     * Основной метод расчета количества обучающихся в Разделе 4
     *
     * @param array $groupQuery
     * @param int $focus
     * @return array
     */
    public function calculateParticipantsSection4(array $groupQuery, int $focus = -1, string $endDate = null) : array
    {
        $focusArr = [$focus];
        if ($focus == -1) {
            $focusArr = array_keys(Yii::$app->focus->getList());
        }

        $queryFocus = $this->groupBuilder->filterGroupsByFocuses(clone $groupQuery['all'], $focusArr);
        $groupsFocus = $this->groupRepository->findAll($queryFocus);

        $participantsQueriesAll = $this->createParticipantQuery($groupsFocus, $endDate);
        $participantsQueriesAges = $this->createParticipantQueryByAges($groupsFocus, $endDate);
        $participantsAll = $this->participantRepository->findAll($participantsQueriesAll['all']);
        $participantsAges = [];
        foreach ($participantsQueriesAges as $index => $query) {
            $participantsAges[$index] = count(
                array_unique(
                    ArrayHelper::getColumn(
                        $this->participantRepository->findAll($query),
                        'participant_id'
                    )
                )
            );
        }

        return [
            'all' => count(array_unique(ArrayHelper::getColumn($participantsAll, 'participant_id'))),
            'ages' => $participantsAges,
        ];
    }





    /**
     * Основной метод расчета количества обучающихся в Разделе 5
     *
     * @param array $groupQuery
     * @param int $focus
     * @return array
     */
    public function calculateParticipantsSection5(array $groupQuery, int $focus, string $endDate): array
    {
        $queryFocus = $this->groupBuilder->filterGroupsByFocuses(clone $groupQuery['all'], [$focus]);

        $budgetGroupQuery = $this->groupBuilder->filterGroupsByBudget(clone $queryFocus, [TrainingGroupWork::IS_BUDGET]);
        $commerceGroupQuery = $this->groupBuilder->filterGroupsByBudget(clone $queryFocus, [TrainingGroupWork::NO_BUDGET]);

        $budgetGroupAll = $this->groupRepository->findAll($budgetGroupQuery);
        $commerceGroupAll = $this->groupRepository->findAll($commerceGroupQuery);

        $participantsBudgetQuery = $this->createParticipantQuery($budgetGroupAll, $endDate);
        $participantsCommerceQuery = $this->createParticipantQuery($commerceGroupAll, $endDate);

        $participantsBudgetAll = $this->participantRepository->findAll($participantsBudgetQuery['all']);
        $participantsCommerceAll = $this->participantRepository->findAll($participantsCommerceQuery['all']);

        $budgetIds = array_unique(ArrayHelper::getColumn($participantsBudgetAll, 'participant_id'));
        $commerceIds = array_unique(ArrayHelper::getColumn($participantsCommerceAll, 'participant_id'));

        return [
            'budget' => count(array_diff($budgetIds, $commerceIds)),
            'commerce' => count(array_diff($commerceIds, $budgetIds)),
            'budget_and_commerce' => count(array_intersect($budgetIds, $commerceIds)),
        ];
    }

    /**
     * Запросы на получение всех подходящих учебных групп
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function createGroupQuery(string $startDate, string $endDate)
    {
        $query = $this->groupBuilder->query();
        $query = $this->groupBuilder->filterGroupsByDates($query, $startDate, $endDate);
        $queryPersonal = $this->groupBuilder->filterGroupsByAllowRemote(clone $query, [AllowRemoteDictionary::ONLY_PERSONAL]);
        $queryRemote = $this->groupBuilder->filterGroupsByAllowRemote(clone $query, [AllowRemoteDictionary::PERSONAL_WITH_REMOTE]);
        $queryNetwork = $this->groupBuilder->filterGroupsByNetwork(clone $query, [TrainingGroupWork::IS_NETWORK]);

        return [
            'all' => $query,
            'personal' => $queryPersonal,
            'remote' => $queryRemote,
            'network' => $queryNetwork
        ];
    }

    /**
     * Запросы на получение обучающихся в группах (с разбивкой по полу)
     *
     * @param TrainingGroupWork[] $groups
     * @return array
     */
    private function createParticipantQuery(array $groups, string $endDate) : array
    {
        $query = $this->participantBuilder->query();
        $query = $this->participantBuilder->joinWith($query, 'participantWork');
        $query = $this->participantBuilder->filterByGroups($query, ArrayHelper::getColumn($groups, 'id'));
        $query = $this->participantBuilder->filterByAge($query, [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17],$endDate );
        $queryAll = $this->participantBuilder->filterBySex(clone $query);
        $queryMale = $this->participantBuilder->filterBySex(clone $query, [PersonInterface::SEX_MALE]);
        $queryFemale = $this->participantBuilder->filterBySex(clone $query, [PersonInterface::SEX_FEMALE]);

        return [
            'all' => $queryAll,
            'male' => $queryMale,
            'female' => $queryFemale
        ];
    }

    /**
     * Запросы на получение обучающихся по возрастам
     *
     * @param array $groups
     * @return array
     */
    private function createParticipantQueryByAges(array $groups, string $endDate = null) : array
    {
        $query = $this->participantBuilder->query();
        $query = $this->participantBuilder->joinWith($query, 'participantWork');
        $query = $this->participantBuilder->filterByGroups($query, ArrayHelper::getColumn($groups, 'id'));

        $queries = [
            '<3' => $this->participantBuilder->filterByAge(clone $query, [0, 1, 2], $endDate),
            '3' => $this->participantBuilder->filterByAge(clone $query, [3], $endDate),
            '4' => $this->participantBuilder->filterByAge(clone $query, [4], $endDate),
            '5' => $this->participantBuilder->filterByAge(clone $query, [5], $endDate),
            '6' => $this->participantBuilder->filterByAge(clone $query, [6], $endDate),
            '7' => $this->participantBuilder->filterByAge(clone $query, [7], $endDate),
            '8' => $this->participantBuilder->filterByAge(clone $query, [8], $endDate),
            '9' => $this->participantBuilder->filterByAge(clone $query, [9], $endDate),
            '10' => $this->participantBuilder->filterByAge(clone $query, [10], $endDate),
            '11' => $this->participantBuilder->filterByAge(clone $query, [11], $endDate),
            '12' => $this->participantBuilder->filterByAge(clone $query, [12], $endDate),
            '13' => $this->participantBuilder->filterByAge(clone $query, [13], $endDate),
            '14' => $this->participantBuilder->filterByAge(clone $query, [14], $endDate),
            '15' => $this->participantBuilder->filterByAge(clone $query, [15], $endDate),
            '16' => $this->participantBuilder->filterByAge(clone $query, [16], $endDate),
            '17' => $this->participantBuilder->filterByAge(clone $query, [17], $endDate),
        ];

        // Добавляем filterBySex к каждому запросу
        foreach ($queries as $key => $ageQuery) {
            $queries[$key] = $this->participantBuilder->filterBySex($ageQuery);
        }

        return $queries;
    }
}