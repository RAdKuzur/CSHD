<?php

namespace frontend\models\search;

use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\work\responsibility\LocalResponsibilityWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * SearchLocalResponsibility represents the model behind the search form of `app\models\common\LocalResponsibility`.
 */
class SearchLocalResponsibility extends LocalResponsibilityWork
{
    public $responsibilityTypeStr;
    public $branchStr;
    public $auditoriumStr;
    public $peopleStampStr;
    public $regulationStr;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'responsibility_type', '$branch', 'auditorium_id', 'people_stamp_id', 'regulation_id'], 'integer'],
            [['files'], 'safe'],
            [['responsibilityTypeStr', 'branchStr', 'auditoriumStr', 'peopleStampStr', 'regulationStr'], 'string'],
        ];
    }

    public function  __construct(
        $responsibilityTypeStr = '',
        $branchStr= '',
        $auditoriumStr = '',
        $peopleStampStr = '',
        $regulationStr = '')
    {
        
        $this->responsibilityTypeStr = $responsibilityTypeStr;
        $this->branchStr = $branchStr;
        $this->auditoriumStr = $auditoriumStr;
        $this->peopleStampStr = $peopleStampStr;
        $this->regulationStr = $regulationStr;
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
     * Определение параметров загрузки данных
     *
     * @param $params
     * @return void
     */
    public function loadParams($params)
    {
//        if (count($params) > 1) {
//        // TODO! add support methods
//        }

        $this->load($params);
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
        $this->loadParams($params);

        $query = LocalResponsibilityWork::find()
            ->joinWith(['auditorium auditorium', 'peopleStamp.people people', 'regulation regulation']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

//        // grid filtering conditions
//        $query->andFilterWhere([
//            'id' => $this->id,
//            'responsibility_type' => $this->responsibility_type,
//            '$branch' => $this->branch,
//            'auditorium_id' => $this->auditorium_id,
//            'people_stamp_id' => $this->people_stamp_id,
//            'regulation_id' => $this->regulation_id,
//        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    public function filterQueryParams(ActiveQuery $query) {
        $this->filterResponsibilityType($query);
        $this->filterBranch($query);
        $this->filterAuditorium($query);
        $this->filterPeople($query);
        $this->filterRegulation($query);
    }

    /**
     * @param ActiveDataProvider $dataProvider
     * @return void
     */
    public function sortAttributes(ActiveDataProvider $dataProvider)
    {
        $dataProvider->sort->attributes['responsibilityTypeStr'] = [
            'asc' => ['responsibility_type' => SORT_ASC],
            'desc' => ['responsibility_type' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['branchStr'] = [
            'asc' => ['branch' => SORT_ASC],
            'desc' => ['branch' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['auditoriumStr'] = [
            'asc' => ['auditorium.name' => SORT_ASC],
            'desc' => ['auditorium.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['peopleStampStr'] = [
            'asc' => ['people.surname' => SORT_ASC],
            'desc' => ['people.surname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['regulationStr'] = [
            'asc' => ['regulation.name' => SORT_ASC],
            'desc' => ['regulation.name' => SORT_DESC],
        ];
    }

    public function filterResponsibilityType(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->responsibilityTypeStr) && $this->responsibilityTypeStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['local_responsibility.responsibility_type' => $this->responsibilityTypeStr]);
        }
    }

    public function filterBranch(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->branchStr) && $this->branchStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['local_responsibility.branch'=> $this->branchStr]);
        }
    }

    public function filterAuditorium(ActiveQuery  $query)
    {
        if (!StringFormatter::isEmpty($this->auditoriumStr) && $this->auditoriumStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['auditorium.name' => $this->auditoriumStr]);
        }
    }

    public function filterPeople(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->peopleStampStr) && $this->peopleStampStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere([ 'or',
                ['like', 'people.surname' ,$this->peopleStampStr],
                ['like', 'people.firstname', $this->peopleStampStr],
                ['like', 'people.patronymic', $this->peopleStampStr],]
            );
        }
    }

    public function filterRegulation(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->regulationStr) && $this->regulationStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['like', 'regulation.name', $this->regulationStr]);
        }
    }

}
