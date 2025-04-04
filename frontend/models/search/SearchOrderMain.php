<?php

namespace frontend\models\search;

use frontend\models\work\order\DocumentOrderWork;
use common\helpers\DateFormatter;
use frontend\models\work\order\OrderMainWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SearchOrderMain extends OrderMainWork
{
    public $fullNumber;
    public $Date;
    public $orderName;

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $query = OrderMainWork::find()
            ->where(['type' => DocumentOrderWork::ORDER_MAIN])
            ->joinWith('bring');
        if ($this->Date !== '' && $this->Date !== null) {
            $dates = DateFormatter::splitDates($this->Date);
            $query->andWhere(
                ['BETWEEN', 'order_date',
                    DateFormatter::format($dates[0], DateFormatter::dmy_dot, DateFormatter::Ymd_dash),
                    DateFormatter::format($dates[1], DateFormatter::dmy_dot, DateFormatter::Ymd_dash)]);
        }

        if ($this->Date !== '' && $this->Date !== null) {
            $dates = DateFormatter::splitDates($this->Date);
            $query->andWhere(['BETWEEN', 'order_date', $dates[0], $dates[1]]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order_date' => SORT_DESC, 'order_number' => SORT_DESC, 'order_postfix' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['fullNumber'] = [
            'asc' => ['order_number' => SORT_ASC, 'order_postfix' => SORT_ASC],
            'desc' => ['order_number' => SORT_DESC, 'order_postfix' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderDate'] = [
            'asc' => ['order_date' => SORT_ASC],
            'desc' => ['order_date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['executorName'] = [
            'asc' => ['executor_id' => SORT_ASC],
            'desc' => ['executor_id' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['bringName'] = [
            'asc' => ['bring_id' => SORT_ASC],
            'desc' => ['bring_id' => SORT_DESC],
        ];


        $dataProvider->sort->attributes['orderName'] = [
            'asc' => ['order_name' => SORT_ASC],
            'desc' => ['order_name' => SORT_DESC],
        ];

        if (!$this->validate()) {
            return $dataProvider;
        }
        // строгие фильтры

        // гибкие фильтры Like
        $query->andFilterWhere(['like', "CONCAT(order_number, '/', order_postfix)", $this->fullNumber])
            ->andFilterWhere(['like', 'order_name', $this->orderName]);
        return $dataProvider;
    }
}