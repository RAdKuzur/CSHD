<?php

namespace common\components\dictionaries\base;

use InvalidArgumentException;

abstract class BaseDictionary
{
    protected $list;

    public function __construct(array $list = [])
    {
        $this->list = $list;
    }

    public function getList()
    {
        return $this->list;
    }

    public function get($index)
    {
        if (is_null($index)) {
            return null;
        }

        if (array_key_exists($index, $this->list)) {
            return $this->list[$index];
        }
        throw new InvalidArgumentException('Неизвестный индекс');
    }

    /**
     * Кастомная сортировка объектов в $list
     * @return mixed
     */
    abstract public function customSort();
}