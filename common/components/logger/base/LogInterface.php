<?php

namespace common\components\logger\base;

interface LogInterface
{
    const LVL_INFO = 0;
    const LVL_WARNING = 1;
    const LVL_ERROR = 2;

    const TYPE_DEFAULT = 0;
    const TYPE_METHOD = 1;
    const TYPE_CRUD = 2;


    public function write() : int;

    /**
     * На основе дополнительных свойств наследники реализуют метод генерации строки данных
     * @return string json-строка для поля add_data @see LogWork
     */
    public function createAddData() : string;
}