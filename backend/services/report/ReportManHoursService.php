<?php

namespace backend\services\report;

use backend\builders\TrainingGroupReportBuilder;
use backend\forms\report\ManHoursReportForm;
use backend\helpers\ReportHelper;
use backend\services\report\interfaces\ManHoursServiceInterface;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class ReportManHoursService implements ManHoursServiceInterface
{
    private TrainingGroupReportBuilder $builder;
    private TrainingGroupRepository $repository;
    private TrainingGroupLessonRepository $lessonRepository;
    private LessonThemeRepository $lessonThemeRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private VisitRepository $visitRepository;

    private DebugReportService $debugService;

    public function __construct(
        TrainingGroupReportBuilder         $builder,
        TrainingGroupRepository            $repository,
        TrainingGroupLessonRepository      $lessonRepository,
        LessonThemeRepository              $lessonThemeRepository,
        TrainingGroupParticipantRepository $participantRepository,
        VisitRepository                    $visitRepository,
        DebugReportService                 $debugService
    )
    {
        $this->builder = $builder;
        $this->repository = $repository;
        $this->lessonRepository = $lessonRepository;
        $this->lessonThemeRepository = $lessonThemeRepository;
        $this->participantRepository = $participantRepository;
        $this->visitRepository = $visitRepository;
        $this->debugService = $debugService;
    }

    /**
     * Вспомогательная функция для генерации отчетов
     * Возвращает запрос на получение отфильтрованных групп
     *
     * @param array $branches
     * @param array $focuses
     * @param array $allowRemotes
     * @param array $budgets
     * @return ActiveQuery
     */
    private function getTrainingGroupsQueryByFilters(
        array $branches,
        array $focuses,
        array $allowRemotes,
        array $budgets
    ) : ActiveQuery
    {
        $query = $this->builder->query();
        $query = $this->builder->filterGroupsByBranches($query, $branches);
        $query = $this->builder->filterGroupsByFocuses($query, $focuses);
        $query = $this->builder->filterGroupsByAllowRemote($query, $allowRemotes);
        return $this->builder->filterGroupsByBudget($query, $budgets);
    }

    /**
     * Метод подсчета человеко-часов за заданный период и с заданным типом подсчета
     *
     * @param string $startDate
     * @param string $endDate
     * @param int[] $branches
     * @param int[] $focuses
     * @param int[] $allowRemotes
     * @param int[] $budgets
     * @param int $calculateType
     * @param int[] $teacherIds передаются id из таблицы {@see PeopleStamp}, не из {@see People}
     * @return array
     */
    public function calculateManHours(
        string $startDate,
        string $endDate,
        array $branches,
        array $focuses,
        array $allowRemotes,
        array $budgets,
        int $calculateType,
        array $teacherIds = [],
        int $mode = ReportFacade::MODE_PURE
    ) : array
    {
        $query = $this->getTrainingGroupsQueryByFilters($branches, $focuses, $allowRemotes, $budgets);

        $query = $this->builder->filterGroupsBetweenDates($query, $startDate, $endDate);
        $groups = $this->repository->findAll($query);

        $participants = $this->participantRepository->getParticipantsFromGroups(
            ArrayHelper::getColumn($groups, 'id')
        );

        $visits = $this->visitRepository->getByTrainingGroupParticipants(
            ArrayHelper::getColumn($participants, 'id')
        );

        $teacherLessonIds = ArrayHelper::getColumn(
            $this->lessonThemeRepository->getByTeacherIds($teacherIds),
            'training_group_lesson_id'
        );

        $result = 0;
        foreach ($visits as $visit) {
            /** @var VisitWork $visit */
            $lessons = VisitLesson::fromString($visit->lessons, $this->lessonRepository);
            foreach ($lessons as $lesson) {
                $result += ReportHelper::checkVisitLesson($lesson, $startDate, $endDate, $calculateType, $teacherLessonIds);
            }
        }

        return [
            'result' => $result,
            'debugData' => $mode == ReportFacade::MODE_DEBUG ?
                $this->debugService->createManHoursDebugData($groups, $startDate, $endDate, $calculateType, $teacherIds) :
                ''
        ];
    }


    /**
     * Метод подсчета обучающихся за заданный период и заданным типом/подтипом подсчета
     *
     * @param string $startDate
     * @param string $endDate
     * @param array $branches
     * @param array $focuses
     * @param array $allowRemotes
     * @param array $budgets
     * @param int[] $calculateTypes типы периодов для поиска групп
     * @param int $calculateSubtype подтип для фильтрации обучающихся (уникальные/все)
     * @param int[] $teacherIds передаются id из таблицы {@see PeopleStamp}, не из {@see People}
     * @return array
     */
    public function calculateParticipantsByPeriod(
        string $startDate,
        string $endDate,
        array $branches,
        array $focuses,
        array $allowRemotes,
        array $budgets,
        array $calculateTypes,
        int $calculateSubtype,
        array $teacherIds = [],
        int $mode = ReportFacade::MODE_PURE
    ) : array
    {
        $query = $this->getTrainingGroupsQueryByFilters($branches, $focuses, $allowRemotes, $budgets);
        // для подсчета уникальных игнорируем разделы (по таймингам)
        if ($calculateSubtype === ManHoursReportForm::PARTICIPANTS_UNIQUE) {
            $tempQuery = $this->builder->filterGroupsByDates(clone $query, $startDate, $endDate, $calculateTypes);
            $tempQuery = $this->builder->filterGroupsByTeachers($tempQuery, $teacherIds);
            $groups = $this->repository->findAll($tempQuery);

            $participants = $this->participantRepository->getParticipantsFromGroups(
                ArrayHelper::getColumn($groups, 'id')
            );
            $uniqueParticipants = array_reduce($participants, function ($carry, $item) {
                $participantId = $item->participant_id;
                if (!isset($carry[$participantId])) {
                    $carry[$participantId] = $item;
                }
                return $carry;
            }, []);

            $participants = $this->participantRepository->getByIds(
                ArrayHelper::getColumn(
                    $uniqueParticipants,
                    'id'
                )
            );

            $result = count($participants);
        }
        // для подсчета всех - проверяем каждый раздел в отдельности
        else {
            $result = [];
            $participants = [];
            foreach ($calculateTypes as $calculateType) {
                $tempQuery = $this->builder->filterGroupsByDates(clone $query, $startDate, $endDate, [$calculateType]);
                $tempQuery = $this->builder->filterGroupsByTeachers($tempQuery, $teacherIds);
                $groups = $this->repository->findAll($tempQuery);

                $tempParticipants = $this->participantRepository->getParticipantsFromGroups(
                    ArrayHelper::getColumn($groups, 'id')
                );

                $result[$calculateType] = count($tempParticipants);
                $participants = array_merge($participants, $tempParticipants);
            }
        }

        return [
            'result' => $result,
            'debugData' => $mode == ReportFacade::MODE_DEBUG ?
                $this->debugService->createParticipantDebugData($participants) :
                ''
        ];
    }
}