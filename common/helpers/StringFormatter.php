<?php

namespace common\helpers;

use common\helpers\search\SearchFieldHelper;
use http\Exception\InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Reader\Xls\MD5;
use yii\helpers\Html;

class StringFormatter
{
    const FORMAT_RAW = 1;
    const FORMAT_LINK = 2;
    const FORMAT_NUMBER = 3;

    const STYLE_CAMEL = 1; // camelCase
    const STYLE_PASCAL = 2; // PascalCase
    const STYLE_SNAKE = 3; // snake_case
    const STYLE_SCREAMING_SNAKE = 4; // SCREAMING_SNAKE_CASE
    const STYLE_KEBAB = 5; // kebab-case
    const STYLE_TRAIN = 6; // Train-Case
    const STYLE_FLAT = 7; // flatcase

    public static function getFormats()
    {
        return [
            self::FORMAT_RAW => 'Обычная строка',
            self::FORMAT_LINK => 'Ссылка типа \<a\>',
            self::FORMAT_NUMBER => 'Строку в число',
        ];
    }

    /**
     * Функция для проверка на пустоту. Стандартная empty() считается что "0" это пусто
     * Примеры: '', ' ' - вернет true, "0" - вернет false
     *
     * @param $variable
     * @return bool
     */
    public static function isEmpty($variable)
    {
        return strlen(trim($variable)) === 0;
    }

    /**
     * Преобразование пустой строки в -1, а всех остальных значений в числовые.
     * Функция необходима для трансформации инпута фильтров в серчи
     *
     * @param $variable
     * @return int
     */
    public static function stringAsInt($variable)
    {
        return self::isEmpty($variable) ? SearchFieldHelper::EMPTY_FIELD : (int) $variable;
    }

    public static function stringAsLink($name, $url)
    {
        return Html::a($name, $url);
    }

    public static function CutFilename($filename, $maxlength = 200)
    {
        $result = '';
        $splitName = explode("_", $filename);
        $i = 0;
        while (strlen($result) < $maxlength - strlen($splitName[$i]) && $i < count($splitName)) {
            $result = $result."_".$splitName[$i];
            $i++;
        }

        return mb_substr($result, 1);
    }

    public static function removeUntilFirstSlash($string) {
        $firstSlashPosition = strpos($string, '/');

        if ($firstSlashPosition !== false) {
            return '/' . substr($string, $firstSlashPosition + 1);
        }

        return $string;
    }

    public static function getLastSegmentBySlash($string) {
        $lastSlashPos = strrpos($string, '/');

        if ($lastSlashPos !== false) {
            return substr($string, $lastSlashPos + 1);
        }

        return $string;
    }

    public static function getLastSegmentByBackslash($string) {
        $lastSlashPos = strrpos($string, '\\');

        if ($lastSlashPos !== false) {
            return substr($string, $lastSlashPos + 1);
        }

        return $string;
    }

    public static function createHash(string $str)
    {
        return MD5($str);
    }

    public static function formatStyle(string $str, int $from, int $to = self::STYLE_FLAT)
    {
        if ($from == $to) {
            return $str;
        }

        if ($from == self::STYLE_FLAT) {
            throw new InvalidArgumentException('Невозможно преобразовать строку из FlatCase');
        }

        // Разбиваем строку на слова в зависимости от исходного стиля
        switch ($from) {
            case self::STYLE_CAMEL:
                $words = preg_split('/(?=[A-Z])/', lcfirst($str));
                break;
            case self::STYLE_PASCAL:
                $words = preg_split('/(?=[A-Z])/', $str);
                break;
            case self::STYLE_SNAKE:
                $words = explode('_', $str);
                break;
            case self::STYLE_SCREAMING_SNAKE:
                $words = explode('_', strtoupper($str));
                break;
            case self::STYLE_KEBAB:
                $words = explode('-', $str);
                break;
            case self::STYLE_TRAIN:
                $words = explode('-', ucwords(strtolower($str), '-'));
                break;
            default:
                $words = [$str];
                break;
        }

        // Собираем строку в новом стиле
        switch ($to) {
            case self::STYLE_CAMEL:
                return lcfirst(implode('', array_map('ucfirst', $words)));
            case self::STYLE_PASCAL:
                return implode('', array_map('ucfirst', $words));
            case self::STYLE_SNAKE:
                return implode('_', array_map('strtolower', $words));
            case self::STYLE_SCREAMING_SNAKE:
                return strtoupper(implode('_', array_map('strtoupper', $words)));
            case self::STYLE_KEBAB:
                return implode('-', array_map('strtolower', $words));
            case self::STYLE_TRAIN:
                return implode('-', array_map('ucfirst', $words));
            case self::STYLE_FLAT:
            default:
                return strtolower(implode('', $words));
        }
    }

    /**
     * Возвращает доменное имя из любого валидного URL
     *
     * @param string $url
     * @return string
     */
    public static function getDomainName(string $url) : string
    {
        $url = preg_replace('/^https?:\/\//i', '', $url);
        $parts = explode('/', $url);
        $domain = array_shift($parts);

        if (($pos = strpos($domain, ':')) !== false) {
            $domain = substr($domain, 0, $pos);
        }

        return $domain;
    }
}