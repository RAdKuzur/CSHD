<?php

namespace common\helpers\common;

use InvalidArgumentException;

class RequestHelper
{
    const TEXT = 1;
    const NUMBER = 2;
    const CHECKBOX = 3;
    const DATE = 4;

    public static function getTypes()
    {
        return [
            self::TEXT => 'Текстовое поле',
            self::NUMBER => 'Числовое поле',
            self::CHECKBOX => 'Поле-чекбокс',
            self::DATE => 'Поле даты'
        ];
    }

    public static function getDataFromPost(array $post, string $name, int $type, array $params = [])
    {
        if (!array_key_exists($type, self::getTypes())) {
            throw new InvalidArgumentException('Неизвестный тип поля');
        }

        if (!array_key_exists($name, $post)) {
            throw new InvalidArgumentException('Неизвестный ключ массива post-запроса');
        }

        $result = [];

        foreach ($post[$name] as $item) {
            if (($type !== self::CHECKBOX) || ($item != 0)) {
                $result[] = $item;
            }
        }

        return $result;
    }
}