<?php

namespace backend\builders;

use backend\forms\report\ManHoursReportForm;
use common\helpers\common\QueryHelper;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class TrainingGroupReportBuilder
{
    private TrainingProgramRepository $programRepository;
    private TeacherGroupRepository $teacherGroupRepository;

    public function __construct(
        TrainingProgramRepository $programRepository,
        TeacherGroupRepository $teacherGroupRepository
    )
    {
        $this->programRepository = $programRepository;
        $this->teacherGroupRepository = $teacherGroupRepository;
    }

    /**
     * @return ActiveQuery
     */
    public function query() : ActiveQuery
    {
        return TrainingGroupWork::find();
    }

    /**
     * Фильтр учебных групп по отделам
     *
     * @param ActiveQuery $query
     * @param int[] $branches
     * @return ActiveQuery
     */
    public function filterGroupsByBranches(ActiveQuery $query, array $branches) : ActiveQuery
    {
        return $query->andWhere(['IN', 'branch', $branches]);
    }

    /**
     * Фильтр учебных групп по основе (бюджет/внебюджет)
     *
     * @param ActiveQuery $query
     * @param int[] $budget
     * @return ActiveQuery
     */
    public function filterGroupsByBudget(ActiveQuery $query, array $budget) : ActiveQuery
    {
        return $query->andWhere(['IN', 'budget', $budget]);
    }

    /**
     * Фильтр учебных групп по направленности (проверяется образовательная программа)
     *
     * @param ActiveQuery $query
     * @param int[] $focuses
     * @return ActiveQuery
     */
    public function filterGroupsByFocuses(ActiveQuery $query, array $focuses) : ActiveQuery
    {
        $programIds = ArrayHelper::getColumn(
            $this->programRepository->getByFocuses($focuses),
            'id'
        );

        return $query->andWhere(['IN', 'training_program_id', $programIds]);
    }

    /**
     * Фильтр учебных групп по форме реализации (проверяется образовательная программа)
     *
     * @param ActiveQuery $query
     * @param int[] $allowRemotes
     * @return ActiveQuery
     */
    public function filterGroupsByAllowRemote(ActiveQuery $query, array $allowRemotes) : ActiveQuery
    {
        $programIds = ArrayHelper::getColumn(
            $this->programRepository->getByAllowRemotes($allowRemotes),
            'id'
        );

        return $query->andWhere(['IN', 'training_program_id', $programIds]);
    }

    /**
     * Фильтр учебных групп по датам
     * Если группа любой частью срока обучения попадает в данный промежуток - она будет учтена
     *
     * @param ActiveQuery $query
     * @param string $date1
     * @param string $date2
     * @return ActiveQuery
     */
    public function filterGroupsBetweenDates(ActiveQuery $query, string $date1, string $date2) : ActiveQuery
    {
        return $this->filterGroupsByDates(
            $query,
            $date1,
            $date2,
            [
                ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN,
                ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER,
                ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN,
                ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER
            ]);
    }

    public function filterGroupsByDates(ActiveQuery $query, string $date1, string $date2, array $types = []) : ActiveQuery
    {
        $conditions = ['or'];
        if (in_array(ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN, $types)) {
            $conditions[] = QueryHelper::getGroupsStartBeforeFinishInDates($date1, $date2);
        }
        if (in_array(ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER, $types)) {
            $conditions[] = QueryHelper::getGroupsStartInFinishAfterDates($date1, $date2);
        }
        if (in_array(ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN, $types)) {
            $conditions[] = QueryHelper::getGroupsStartInFinishInDates($date1, $date2);
        }
        if (in_array(ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER, $types)) {
            $conditions[] = QueryHelper::getGroupsStartBeforeFinishAfterDates($date1, $date2);
        }

        return $query->andWhere($conditions);
    }

    /**
     * Фильтр учебных групп по педагогам
     *
     * @param ActiveQuery $query
     * @param int[] $teacherIds
     * @return ActiveQuery
     */
    public function filterGroupsByTeachers(ActiveQuery $query, array $teacherIds) : ActiveQuery
    {
        if (count($teacherIds) === 0) {
            return $query;
        }

        $groupIds = ArrayHelper::getColumn(
            $this->teacherGroupRepository->getAllFromTeacherIds($teacherIds),
            'training_group_id'
        );

        return $query->andWhere(['IN', 'id', $groupIds]);
    }

    public function filterGroupsByNetwork(ActiveQuery $query, array $networks = [TrainingGroupWork::NO_NETWORK, TrainingGroupWork::IS_NETWORK]) : ActiveQuery
    {
        return $query->andWhere(['IN', 'is_network', $networks]);
    }
}