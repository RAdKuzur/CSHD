<?php

namespace frontend\models\search;

use frontend\models\work\responsibility\LocalResponsibilityWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchLocalResponsibility represents the model behind the search form of `app\models\common\LocalResponsibility`.
 */
class SearchLocalResponsibility extends LocalResponsibilityWork
{
    public $responsibilityTypeStr;
    public $branchStr;
    public $auditoriumStr;
    public $peopleStr;
    public $regulationStr;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'responsibility_type_id', 'branch_id', 'auditorium_id', 'people_id', 'regulation_id'], 'integer'],
            [['files'], 'safe'],
            [['responsibilityTypeStr', 'branchStr', 'auditoriumStr', 'peopleStr', 'regulationStr'], 'string'],
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
        $query = LocalResponsibilityWork::find();

        $query->joinWith(['auditorium auditorium', 'peopleStamp peopleStamp', 'regulation regulation']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        /*$dataProvider->sort->attributes['responsibilityTypeStr'] = [
            'asc' => ['responsibilityType.name' => SORT_ASC],
            'desc' => ['responsibilityType.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['branchStr'] = [
            'asc' => ['branch.name' => SORT_ASC],
            'desc' => ['branch.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['auditoriumStr'] = [
            'asc' => ['auditorium.name' => SORT_ASC],
            'desc' => ['auditorium.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['peopleStr'] = [
            'asc' => ['people.secondname' => SORT_ASC],
            'desc' => ['people.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['regulationStr'] = [
            'asc' => ['regulation.name' => SORT_ASC],
            'desc' => ['regulation.name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'responsibility_type_id' => $this->responsibility_type_id,
            'branch_id' => $this->branch_id,
            'auditorium_id' => $this->auditorium_id,
            'people_id' => $this->people_id,
            'regulation_id' => $this->regulation_id,
        ]);

        $query->andFilterWhere(['like', 'files', $this->files])
            ->andFilterWhere(['like', 'responsibilityType.name', $this->responsibilityTypeStr])
            ->andFilterWhere(['like', 'branch.name', $this->branchStr])
            ->andFilterWhere(['like', 'auditorium.name', $this->auditoriumStr])
            ->andFilterWhere(['like', 'people.secondname', $this->peopleStr])
            ->andFilterWhere(['like', 'regulation.name', $this->regulationStr]);*/

        return $dataProvider;
    }
}
