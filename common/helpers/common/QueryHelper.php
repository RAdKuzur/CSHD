<?php

namespace common\helpers\common;

class QueryHelper
{
    /**
     * Условие для запроса по времени обучения группы
     * Выбирает все группы, которые начали обучение до указанного периода и завершили в указанный период
     *
     * @param string $date1
     * @param string $date2
     * @return array
     */
    public static function getGroupsStartBeforeFinishInDates(string $date1, string $date2)
    {
        return [
            'and',
            ['<', 'start_date', $date1],
            ['BETWEEN', 'finish_date', $date1, $date2]
        ];
    }

    /**
     * Условие для запроса по времени обучения группы
     * Выбирает все группы, которые начали обучение в указанный период и завершили после указанного периода
     *
     * @param string $date1
     * @param string $date2
     * @return array
     */
    public static function getGroupsStartInFinishAfterDates(string $date1, string $date2)
    {
        return [
            'and',
            ['BETWEEN', 'start_date', $date1, $date2],
            ['>', 'finish_date', $date2]
        ];
    }

    /**
     * Условие для запроса по времени обучения группы
     * Выбирает все группы, которые начали и завершили обучение в указанный период
     *
     * @param string $date1
     * @param string $date2
     * @return array
     */
    public static function getGroupsStartInFinishInDates(string $date1, string $date2)
    {
        return [
            'and',
            ['BETWEEN', 'start_date', $date1, $date2],
            ['BETWEEN', 'finish_date', $date1, $date2]
        ];
    }

    /**
     * Условие для запроса по времени обучения группы
     * Выбирает все группы, которые начали обучение до указанного периода и завершили после указанного периода
     *
     * @param string $date1
     * @param string $date2
     * @return array
     */
    public static function getGroupsStartBeforeFinishAfterDates(string $date1, string $date2)
    {
        return [
            'and',
            ['<', 'start_date', $date1],
            ['>', 'finish_date', $date2]
        ];
    }
}