<?php

namespace common\helpers;

use DateTime;
use DomainException;

class CompareHelper
{
    /**
     * Возможные результаты сравнений/проверок
     * RESULT_CORRECT - все ОК
     * RESULT_DOUBTFUL - результат сомнительный/требует уточнения
     * RESULT_INCORRECT - результат некорректен/нереалистичен
     */
    const RESULT_CORRECT = 0;
    const RESULT_DOUBTFUL = 1;
    const RESULT_INCORRECT = 2;

    /**
     * Проверка даты рождения участника деятельности на корректность
     * @param string $participantBirthdate дата рождения участника
     * @param string $maximumTrueBirthdate максимально допустимая дата рождения
     * @param string $maximumRealBirthdate максимальная реалистичная дата рождения
     * @param int $guaranteedTrue отмечен ли участник как верный (если да - то $maximumTrueBirthdate не учитывается)
     * @return int
     */
    public static function compareParticipantBirthdate(string $participantBirthdate, string $maximumTrueBirthdate, string $maximumRealBirthdate, int $guaranteedTrue = 0)
    {
        $newDate = new DateTime('-3 year');
        $newDateFormatted = $newDate->format('Y-m-d');

        $isAboveMaximumTrue = $participantBirthdate < $maximumTrueBirthdate || $participantBirthdate > $newDateFormatted;
        $isAboveMaximumReal = $participantBirthdate < $maximumRealBirthdate || $participantBirthdate > $newDateFormatted;

        if (($isAboveMaximumTrue && $guaranteedTrue) || $isAboveMaximumReal) {
            return self::RESULT_INCORRECT;
        }

        return self::RESULT_CORRECT;
    }

    /**
     * Сравнение массива строк с эталонным массивом с использованием расстояния Левенштейна
     * Возвращает RESULT_CORRECT если массивы совпадают
     * @param array $data входной массив
     * @param array $dataStandard эталонный массив
     * @param int $accuracy точность сравнения
     * @return int
     */
    public static function compareStrings(array $data, array $dataStandard, int $accuracy)
    {
        if (count($data) !== count($dataStandard)) {
            throw new DomainException('Размеры массивов $data и $dataStandard не совпадают');
        }

        $lev = 0;
        foreach ($data as $i => $one) {
            $lev += levenshtein($one, $dataStandard[$i]);
            if ($lev >= $accuracy) {
                return self::RESULT_INCORRECT;
            }
        }

        return self::RESULT_CORRECT;
    }

    /**
     * Проверяет, является ли входная строка корректным e-mail
     * @param string $str
     * @return int
     */
    public static function isEmail(string $str) {
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        return preg_match($pattern, $str) === 1 ? self::RESULT_CORRECT : self::RESULT_INCORRECT;
    }
}