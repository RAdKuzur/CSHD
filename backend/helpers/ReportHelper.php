<?php

namespace backend\helpers;

use backend\forms\report\ManHoursReportForm;
use common\models\scaffold\TrainingGroupLesson;
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

//    public static function calculateAttendanceOptimized(string $lessonsJson, int $calculateType)
//    {
//        $count = self::calculateVisitsByType($lessonsJson, VisitWork::ATTENDANCE)
//            + self::calculateVisitsByType($lessonsJson, VisitWork::DISTANCE);
//        if ($calculateType == ManHoursReportForm::MAN_HOURS_ALL) {
//            $count += self::calculateVisitsByType($lessonsJson, VisitWork::NO_ATTENDANCE);
//        }
//
//        return $count;
//    }

    public static function calculateAttendanceOptimized(
        string $lessonsJson,
        int $calculateType,
        string $startDate,
        string $endDate
    ): int {
        $count = 0;
        $lessons = json_decode($lessonsJson, true);

        // Шаг 1: собрать все lesson_id
        $lessonIds = [];
        foreach ($lessons as $lesson) {
            $lessonIds[] = $lesson['lesson_id'];
        }
        $lessonIds = array_unique($lessonIds);

        if (empty($lessonIds)) {
            return 0;
        }

        // Шаг 2: одним запросом получить все уроки с датами
        $lessonsData = TrainingGroupLesson::find()
            ->select(['id', 'lesson_date'])
            ->where(['id' => $lessonIds])
            ->asArray()
            ->all();

        // Шаг 3: построить карту id => дата
        $lessonDateMap = [];
        foreach ($lessonsData as $ld) {
            $lessonDateMap[$ld['id']] = $ld['lesson_date'];
        }

        // Шаг 4: посчитать по нужным условиям, используя массив с датами
        foreach ($lessons as $lesson) {
            $lessonId = $lesson['lesson_id'];
            if (!isset($lessonDateMap[$lessonId])) {
                // урока нет в базе, пропускаем
                continue;
            }

            $lessonDate = $lessonDateMap[$lessonId];
            if ($lessonDate >= $startDate && $lessonDate <= $endDate) {
                if (in_array($lesson['status'], [VisitWork::ATTENDANCE, VisitWork::DISTANCE])) {
                    $count++;
                } elseif (
                    $calculateType == ManHoursReportForm::MAN_HOURS_ALL
                ) {
                    $count++;
                }
            }
        }

        return $count;
    }
    private static function calculateVisitsByType(string $lessonsJson, int $type)
    {
        $pattern = '/"status":' . $type . '/';
        preg_match_all($pattern, $lessonsJson, $matches);
        return count($matches[0]);
    }
}