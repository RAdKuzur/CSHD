<?php

namespace common\components\interfaces;

use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

interface SearchInterfaces {
    public function rules();

    public function loadParams($params);

    public function search($params);

    public function sortAttributes(ActiveDataProvider $dataProvider);

    public function filterQueryParams(ActiveQuery $query);
}