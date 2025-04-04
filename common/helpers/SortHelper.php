<?php

namespace common\helpers;

use DomainException;
use InvalidArgumentException;

class SortHelper
{
    const ORDER_TYPE_ID = 0;
    const ORDER_TYPE_FIO = 1;

    public static function getOrderTypes()
    {
        return [
            self::ORDER_TYPE_ID => 'Сортировка по ID записи',
            self::ORDER_TYPE_FIO => 'Сортировка по ФИО (требует наличия полей surname, firstname, patronymic в таблице)',
        ];
    }

    public static function orderedAvailable($object, $orderedType, $orderDirection)
    {
        if (!array_key_exists($orderedType, SortHelper::getOrderTypes())) {
            throw new InvalidArgumentException('Некорректный тип сортировки');
        }

        if (!($orderDirection == SORT_ASC || $orderDirection == SORT_DESC)) {
            throw new InvalidArgumentException('Некорректное направление сортировки');
        }

        switch ($orderedType) {
            case self::ORDER_TYPE_ID:
                return $object->hasAttribute('id');
            case self::ORDER_TYPE_FIO:
                return $object->hasAttribute('surname') &&
                    $object->hasAttribute('firstname') &&
                    $object->hasAttribute('patronymic');
            default:
                throw new DomainException('Что-то пошло не так');
        }
    }
}