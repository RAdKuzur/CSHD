<?php

namespace backend\services\report\interfaces;

use backend\services\report\ReportFacade;

interface ManHoursServiceInterface
{
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
    ) : array;
}