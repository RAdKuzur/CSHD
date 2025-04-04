<?php

namespace common\helpers;

use DateTime;
use InvalidArgumentException;

class DateFormatter
{
    const Ymd_dash = 1;
    const Ymd_dot = 2;
    const dmY_dash = 3;
    const dmY_dot = 4;
    const dmy_dash = 5;
    const dmy_dot = 6;
    const mdY_slash = 7;
    const His_colon = 8;
    const Hi_colon = 9;
    const Ymd_without_separator = 10;
    const dm_dot = 11;


    const DEFAULT_STUDY_YEAR_RANGE = '2018:2030';     // заданная по умолчанию ограничения по дате
    const DEFAULT_STUDY_YEAR_START = '2018-01-01';    // заданная по умолчанию ограничение по начальной дате

    public static function getFormats()
    {
        return [
            self::Ymd_dash => 'Y-m-d',
            self::Ymd_dot => 'Y.m.d',
            self::dmY_dash => 'd-m-Y',
            self::dmY_dot => 'd.m.Y',
            self::dmy_dash => 'd-m-y',
            self::dmy_dot => 'd.m.y',
            self::mdY_slash => 'm/d/Y',
            self::Ymd_without_separator => 'Ymd',
            self::Hi_colon => 'H:i',
            self::His_colon => 'H:i:s',
            self::dm_dot => 'd.m',
        ];
    }

    public static function get($index)
    {
        $formats = self::getFormats();
        if (!array_key_exists($index, $formats)) {
            throw new InvalidArgumentException('Неизвестный формат даты');
        }

        return $formats[$index];
    }

    public static function splitDates($dates)
    {
        $pairDates = explode(' - ', $dates);
        if (count($pairDates) != 2) {
            throw new InvalidArgumentException('Некорректный формат дат');
        }

        return $pairDates;
    }

    public static function format($data, $baseType, $targetType)
    {
        $datetime = DateTime::createFromFormat(self::get($baseType), $data);
        return $datetime ? $datetime->format(self::get($targetType)) : $data;
    }

    public static function createMonthPeriod(string $startDate, int $leftOffset, int $rightOffset)
    {
        $startDate = new DateTime($startDate);

        // Вычисляем дату -leftOffset месяцев
        $leftDate = clone $startDate;
        $leftDate->modify("-$leftOffset months");

        // Вычисляем дату +rightOffset месяцев
        $rightDate = clone $startDate;
        $rightDate->modify("+$rightOffset months");

        return [
            $leftDate->format('Y-m-d'),
            $rightDate->format('Y-m-d')
        ];
    }
}