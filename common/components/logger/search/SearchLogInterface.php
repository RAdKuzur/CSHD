<?php

namespace common\components\logger\search;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

interface SearchLogInterface
{
    /**
     * Составляет ActiveQuery по основным полям таблицы и возвращает результат
     * @return array|ActiveRecord[]
     */
    public function findByBaseData() : array;
}