<?php

namespace backend\helpers;

use backend\forms\report\ManHoursReportForm;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;

class ReportHelper
{
    /**
     * Вспомогательная функция проверки учета занятия в отчете по человеко-часам
     *
     * @param VisitLesson $visitLesson
     * @param string $startDate
     * @param string $endDate
     * @param int $calculateType
     * @param int[] $teacherLessonIds
     * @return int
     */
    public static function checkVisitLesson(VisitLesson $visitLesson, string $startDate, string $endDate, int $calculateType, array $teacherLessonIds = []): int
    {
        $conditionTeacher = true;
        if (count($teacherLessonIds) > 0) {
            $conditionTeacher = in_array($visitLesson->lessonId, $teacherLessonIds);
        }

        if (
            $visitLesson->lesson &&
            ($visitLesson->lesson->lesson_date >= $startDate && $visitLesson->lesson->lesson_date <= $endDate) &&
            (($visitLesson->status == VisitWork::ATTENDANCE || $visitLesson->status == VisitWork::DISTANCE) ||
                ($calculateType == ManHoursReportForm::MAN_HOURS_ALL && $visitLesson->status == VisitWork::NO_ATTENDANCE)) &&
            $conditionTeacher
        ) {
            return 1;
        }

        return 0;
    }
}