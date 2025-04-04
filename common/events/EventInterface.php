<?php

namespace common\events;

interface EventInterface
{
    public function isSingleton() : bool;

    /**
     * Функция выполнения. Должна возвращать массив запросов к БД (если они есть)
     * @return mixed
     */
    public function execute();
}