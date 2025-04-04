<?php

namespace common\helpers;

class OrderNumberHelper
{
    public static function splitString($input) {
        // Используем функцию explode для разделения строки по символу '/'
        $words = explode('/', $input);
        return $words;
    }
    public static function sortArrayByOrderNumber(&$array) {
        if($array != NULL) {
            usort($array, function ($a, $b) {
                return strcmp($a[1], $b[1]); // Сравниваем элементы с индексом 1, которые соответствуют order_number
            });
        }
    }
    public static function findByNumberPostfix($array, $numberPostfix)
    {
        if($array != NULL) {
            foreach ($array as $item) {
                if($item[2] == $numberPostfix) {
                    return true;
                }
            }
        }
        else {
            return false;
        }
    }

}