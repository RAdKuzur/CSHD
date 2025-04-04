<?php

namespace common\helpers\common;

class SqlHelper
{
    /**
     * Создает SQL-команду для одноразового события удаления
     *
     * @param string $name имя события
     * @param string $datetime время исполнения события
     * @param string $tableName имя таблицы, из которой производится удаление
     * @param string $condition условие удаления (WHERE `field` != 5)
     * @return string
     */
    public static function createDeleteEvent(string $name, string $datetime, string $tableName, string $condition) : string
    {
        return "CREATE EVENT `$name` 
            ON SCHEDULE AT '$datetime' 
            ON COMPLETION NOT PRESERVE ENABLE
            DO DELETE
            FROM `$tableName` 
            $condition";
    }

    /**
     * Создает SQL-команду для удаления события
     *
     * @param string $name имя события
     * @return string
     */
    public static function dropEvent(string $name) : string
    {
        return "DROP EVENT IF EXISTS `{$name}`";
    }
}