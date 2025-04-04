<?php


namespace frontend\services\educational;


use common\helpers\DateFormatter;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;

class PitchService
{
    /*
     * Смещения от текущей даты в месяцах
     * Выгружаем группы, чьи даты защит попадают в данный промежуток
     */
    const LEFT_MONTH_OFFSET = 3;
    const RIGHT_MONTH_OFFSET = 2;

    private TrainingGroupRepository $groupRepository;

    public function __construct(
        TrainingGroupRepository $groupRepository
    )
    {
        $this->groupRepository = $groupRepository;
    }

    public function getSplittedGroups()
    {
        $period = DateFormatter::createMonthPeriod(date('Y-m-d'), self::LEFT_MONTH_OFFSET, self::RIGHT_MONTH_OFFSET);
        /** @var TrainingGroupWork[] $groups */
        $groups = $this->groupRepository->getGroupPitchInPeriod($period[0], $period[1]);

        $groupsFinished = [];
        $groupsInProgress = [];
        foreach ($groups as $group) {
            if (date('Y-m-d') <= $group->protection_date) {
                $groupsInProgress[] = $group;
            }
            else {
                $groupsFinished[] = $group;
            }
        }

        return [
            'progress' => $groupsInProgress,
            'finished' => $groupsFinished
        ];
    }
}