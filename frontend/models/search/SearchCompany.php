<?php

namespace frontend\models\search;

use frontend\models\work\dictionaries\CompanyWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchCompany represents the model behind the search form of `app\models\common\Company`.
 */
class SearchCompany extends CompanyWork
{
    public $contractorString;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_type'], 'integer'],
            [['name', 'inn'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
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
        $query = CompanyWork::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->attributes['contractorString'] = [
            'asc' => ['is_contractor' => SORT_ASC],
            'desc' => ['is_contractor' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'company_type' => $this->company_type,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'inn', $this->inn]);

        return $dataProvider;
    }
}
