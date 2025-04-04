<?php

namespace common\components\access\pbac;

use common\components\access\pbac\data\PbacEventData;
use common\components\access\pbac\data\PbacLessonData;
use common\repositories\act_participant\ActParticipantBranchRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\event\ForeignEventRepository;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use Yii;
use yii\helpers\ArrayHelper;

class PbacLessonAccess implements PbacComponentInterface
{
    const LESSON_OFFSET_DOWN = 5; // кол-во дней, раньше которого запрещено редактирование явок
    const LESSON_OFFSET_UP = 1; // кол-во дней, позже которых запрещено редактирование явок

    private PbacLessonData $data;
    private TrainingGroupLessonRepository $lessonRepository;

    public function __construct(
        PbacLessonData $data
    )
    {
        $this->data = $data;
        $this->lessonRepository = Yii::createObject(TrainingGroupLessonRepository::class);
    }

    public function getAllowedLessonIds()
    {
        $lessons = $this->lessonRepository->getLessonsFromGroup($this->data->group->id);
        if (
            Yii::$app->rubac->checkPermission($this->data->user->id, 'edit_branch_groups') ||
            Yii::$app->rubac->checkPermission($this->data->user->id, 'edit_all_groups')
        ) {
            return ArrayHelper::getColumn($lessons, 'id');
        }

        return ArrayHelper::getColumn(
            array_filter($lessons, function (TrainingGroupLessonWork $lesson) {
                $currentDate = strtotime("today");
                $lowerBound = strtotime("-" . self::LESSON_OFFSET_DOWN . " days", $currentDate);
                $upperBound = strtotime("+" . self::LESSON_OFFSET_UP . " day", $currentDate);
                $targetDate = strtotime($lesson->lesson_date);

                return $targetDate >= $lowerBound && $targetDate <= $upperBound;
            }),
            'id'
        );
    }
}