<?php

namespace backend\services\report\mock;

use backend\helpers\ReportHelper;
use backend\services\report\interfaces\ManHoursServiceInterface;
use backend\services\report\ReportFacade;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\providers\group_lesson\TrainingGroupLessonMockProvider;
use frontend\models\work\educational\journal\VisitLesson;
use Yii;
use yii\helpers\ArrayHelper;

class ReportManHoursMockService implements ManHoursServiceInterface
{
    private array $groups;
    private array $participants;
    private array $lessonThemes;
    private array $lessons;
    private array $visits;

    public function __construct(
        array $groups = [],
        array $participants = [],
        array $lessonThemes = [],
        array $lessons = [],
        array $visits = []
    )
    {
        $this->groups = $groups;
        $this->participants = $participants;
        $this->lessonThemes = $lessonThemes;
        $this->lessons = $lessons;
        $this->visits = $visits;
    }

    public function setMockData(
        array $groups = [],
        array $participants = [],
        array $lessonThemes = [],
        array $lessons = [],
        array $visits = []
    )
    {
        $this->groups = $groups;
        $this->participants = $participants;
        $this->lessonThemes = $lessonThemes;
        $this->lessons = $lessons;
        $this->visits = $visits;
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
        $groups = $this->getTrainingGroupsQueryByFilters($startDate, $endDate, $branches, $focuses, $allowRemotes, $budgets);
        $groupIds = ArrayHelper::getColumn($groups, 'id');

        $teacherLessonIds = count($teacherIds) > 0 ?
            ArrayHelper::getColumn(
                array_filter($this->lessonThemes, function ($lessonTheme) use ($teacherIds) {
                    return in_array($lessonTheme['teacher_id'], $teacherIds);
                }),
                'training_group_lesson_id'
            ):
            [];

        $result = 0;
        $lessonData = TrainingGroupLessonMockProvider::convert($this->lessons, ['id', 'training_group_id', 'lesson_date']);
        $lessonData = array_filter($lessonData, function ($lessonMock) use ($groupIds) {
            return in_array($lessonMock['training_group_id'], $groupIds);
        });

        $mockRepository = Yii::createObject(
            TrainingGroupLessonRepository::class,
            ['provider' => Yii::createObject(
                TrainingGroupLessonMockProvider::class,
                    ['data' => $lessonData]
                )
            ]
        );

        foreach ($this->visits as $visit) {
            $lessons = VisitLesson::fromString(
                $visit['lessons'],
                $mockRepository
            );

            foreach ($lessons as $lesson) {
                $result += ReportHelper::checkVisitLesson($lesson, $startDate, $endDate, $calculateType, $teacherLessonIds);
            }
        }

        return [
            'result' => $result,
            'extraData' => [
                'groups' => $groups
            ]
        ];
    }

    public function getTrainingGroupsQueryByFilters(
        string $startDate,
        string $endDate,
        array $branches,
        array $focuses,
        array $allowRemotes,
        array $budgets
    ) : array
    {
        return array_filter($this->groups, function ($group) use ($startDate, $endDate, $branches, $focuses, $allowRemotes, $budgets) {
            $isDateInRange = ($group['start_date'] <= $endDate && $group['finish_date'] >= $startDate);
            $isBranchMatch = empty($branches) || in_array($group['branch'], $branches);
            $isFocusMatch = empty($focuses) || in_array($group['focus'], $focuses);
            $isAllowRemoteMatch = empty($allowRemotes) || in_array($group['allow_remote'], $allowRemotes);
            $isBudgetMatch = empty($budgets) || in_array($group['budget'], $budgets);
            return $isDateInRange && $isBranchMatch && $isFocusMatch && $isAllowRemoteMatch && $isBudgetMatch;
        });
    }
}