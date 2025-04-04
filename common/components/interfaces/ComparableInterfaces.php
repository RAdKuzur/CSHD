<?php

namespace common\components\interfaces;

/**
 * @template T
 */
interface ComparableInterfaces
{
    /**
     * @param T $c1
     * @param T $c2
     * @return bool
     */
    public static function compare($c1, $c2) : bool;
}