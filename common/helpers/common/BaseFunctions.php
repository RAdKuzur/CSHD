<?php

namespace common\helpers\common;

class BaseFunctions
{
    public static function transposeMatrix(array $matrix): array
    {
        $transposed = [];

        foreach ($matrix as $row) {
            foreach ($row as $key => $value) {
                $transposed[$key][] = $value;
            }
        }

        return $transposed;
    }

    public static function monthFromNumbToString($month)
    {
        if ($month === '01') {
            return 'января';
        }
        if ($month === '02') {
            return 'февраля';
        }
        if ($month === '03') {
            return 'марта';
        }
        if ($month === '04') {
            return 'апреля';
        }
        if ($month === '05') {
            return 'мая';
        }
        if ($month === '06') {
            return 'июня';
        }
        if ($month === '07') {
            return 'июля';
        }
        if ($month === '08') {
            return 'августа';
        }
        if ($month === '09') {
            return 'сентября';
        }
        if ($month === '10') {
            return 'октября';
        }
        if ($month === '11') {
            return 'ноября';
        }
        if ($month === '12') {
            return 'декабря';
        }
        else {
            return '______';
        }
    }

    public static function rus2EngTranslit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'j',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'J',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }
}