<?php

namespace frontend\models\search;

use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderTrainingWork;
use frontend\models\search\SearchOrderMain;
use frontend\models\work\order\OrderEventWork;
use common\helpers\DateFormatter;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SearchOrderTraining extends OrderTrainingWork
{
    const ORDER_TYPE = 3;
    public $fullNumber;
    public $Date;
    public $orderName;
    public function rules(){
        return [
            [['id', 'order_copy_id', 'bring_id', 'signed_id', 'executor_id', 'creator_id'], 'integer'],
            [['fullNumber'], 'string'],
            [['Date', 'orderName'], 'safe'],
        ];
    }
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
        $query = OrderEventWork::find()
            ->where(['type' => DocumentOrderWork::ORDER_TRAINING])
            ->joinWith('bring');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order_date' => SORT_DESC, 'order_number' => SORT_DESC, 'order_postfix' => SORT_DESC]]
        ]);


        if (!$this->validate()) {
            return $dataProvider;
        }

        // гибкие фильтры Like
        $query->andFilterWhere(['like', "CONCAT(order_number, '/', order_postfix)", $this->fullNumber])
            ->andFilterWhere(['like', 'order_name', $this->orderName]);
        return $dataProvider;
    }
}