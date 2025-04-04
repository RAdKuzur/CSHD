<?php


namespace common\components\logger\search;


interface SearchDataInterface
{
    public function haveData(string $addData) : bool;
}