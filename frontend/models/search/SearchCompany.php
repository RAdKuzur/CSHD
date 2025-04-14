<?php

namespace frontend\models\search;

use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\work\dictionaries\CompanyWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class SearchCompany extends CompanyWork
{
    public string $name;
    public int $type;
    public int $is_contractor;
    public string $inn;

    const CONTRACTOR = [0 => 'Не явялется контрагентом', 1 => 'Является контрагентом'];

    public function rules()
    {
        return [
            [['id', 'type', 'is_contractor'], 'integer'],
            [['name'], 'string'],
            [['inn'], 'number'],
            [['name', 'inn', 'type', 'is_contractor'], 'safe'],
        ];
    }

    public function __construct(
        string $name = '',
        int $type = SearchFieldHelper::EMPTY_FIELD,
        int $is_contractor = SearchFieldHelper::EMPTY_FIELD,
        string $inn = '',
               $config = []
    )
    {
        parent::__construct($config);
        $this->name = $name;
        $this->type = $type;
        $this->is_contractor = $is_contractor;
        $this->inn = $inn;
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     * @return void
     */
    public function loadParams($params)
    {
        if (count($params) > 1) {
            $params['SearchCompany']['type'] = StringFormatter::stringAsInt($params['SearchCompany']['type']);
            $params['SearchCompany']['is_contractor'] = StringFormatter::stringAsInt($params['SearchCompany']['is_contractor']);
            $params['SearchCompany']['inn'] = StringFormatter::stringAsInt($params['SearchCompany']['inn']);
        }

        $this->load($params);
    }

    public function search($params)
    {
        $this->loadParams($params);

        $query = CompanyWork::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]]
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    /**
     * @param ActiveDataProvider $dataProvider
     * @return void
     */
    public function sortAttributes(ActiveDataProvider $dataProvider)
    {
        $dataProvider->sort->attributes['inn'] = [
            'asc' => ['inn' => SORT_ASC],
            'desc' => ['inn' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['name'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['short_name'] = [
            'asc' => ['short_name' => SORT_ASC],
            'desc' => ['short_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['company_type'] = [
            'asc' => ['company_type' => SORT_ASC],
            'desc' => ['company_type' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['contractorString'] = [
            'asc' => ['is_contractor' => SORT_ASC],
            'desc' => ['is_contractor' => SORT_DESC],
        ];
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query)
    {
        $this->filterName($query);
        $this->filterType($query);
        $this->filterContractor($query);
        $this->filterInn($query);
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterName(ActiveQuery $query) {
        if (!empty($this->name)) {
            $query->andWhere(['or',
                ['like', 'LOWER(name)', mb_strtolower($this->name)],
                ['like', 'LOWER(short_name)', mb_strtolower($this->name)],
            ]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterType(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->type) && $this->type !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['company_type' => $this->type]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterContractor(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->is_contractor) && $this->is_contractor !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['is_contractor' => $this->is_contractor]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterInn(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->inn) && $this->inn !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['inn' => $this->inn]);
        }
    }

}
