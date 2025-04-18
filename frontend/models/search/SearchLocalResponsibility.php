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
    public string $responsibilityTypeStr;
    public string $branchStr;
    public string $auditoriumStr;
    public string $peopleStr;
    public string $regulationStr;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'responsibility_type', '$branch', 'auditorium_id', 'people_stamp_id', 'regulation_id'], 'integer'],
            [['files'], 'safe'],
            [['responsibilityTypeStr', 'branchStr', 'auditoriumStr', 'peopleStr', 'regulationStr'], 'string'],
        ];
    }



    public function  __construct(
        string $responsibilityTypeStr = '',
        string $branchStr= '',
        string $auditoriumStr = '',
        string $peopleStr = '',
        string $regulationStr = '')
    {
        
        $this->responsibilityTypeStr = $responsibilityTypeStr;
        $this->branchStr = $branchStr;
        $this->auditoriumStr = $auditoriumStr;
        $this->peopleStr = $peopleStr;
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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

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

    private function filterResponsibilityType(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->responsibilityTypeStr) && $this->responsibilityTypeStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['local_responsibility.responsibility_type' => $this->responsibilityTypeStr]);
        }
    }

    private function filterBranch(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->branchStr) && $this->branchStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['local_responsibility.branch'=> $this->branchStr]);
        }
    }

    private function filterAuditorium(ActiveQuery  $query)
    {
        if (!StringFormatter::isEmpty($this->auditoriumStr) && $this->auditoriumStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['auditorium.name' => $this->auditoriumStr]);
        }
    }

    private function filterPeople(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->peopleStr) && $this->peopleStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere([ 'or',
                ['like', 'people.surname' ,$this->peopleStr],
                ['like', 'people.firstname', $this->peopleStr],
                ['like', 'people.patronymic', $this->peopleStr],]
            );
        }
    }

    private function filterRegulation(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->regulationStr) && $this->regulationStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['like', 'regulation.name', $this->regulationStr]);
        }
    }

}
