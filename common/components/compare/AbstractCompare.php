<?php

namespace common\components\compare;

abstract class AbstractCompare
{
    abstract static public function compare($c1, $c2) : int;
}