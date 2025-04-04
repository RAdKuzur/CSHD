<?php

namespace console\controllers\seeders;
use DateTimeImmutable;
use DateTimeInterface;
class RandomHelper
{
    public const START_DATE = '2018-01-01';
    public const FINISH_DATE = '2026-01-01';
    public const START_TIME = '00:00:00';
    public const FINISH_TIME = '23:59:59';
    public function randomDate($startDate = self::START_DATE, $endDate = self::FINISH_DATE){
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);
        $randomTimestamp = mt_rand($startTimestamp, $endTimestamp);
        return date('Y-m-d', $randomTimestamp);
    }
    public function randomTime($startTime = self::START_TIME, $endTime = self::FINISH_TIME) {
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);
        $randomTimestamp = mt_rand($startTimestamp, $endTimestamp);
        return date('H:i:s', $randomTimestamp);
    }
    public function randomItem($array){
        return count($array) == 0 ? NULL : $array[array_rand($array)];
    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}