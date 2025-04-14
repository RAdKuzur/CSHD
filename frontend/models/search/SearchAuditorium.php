<?php

namespace frontend\models\search;

use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\work\dictionaries\AuditoriumWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class SearchAuditorium extends AuditoriumWork
{
    public string $globalNumber;
    public string $name;
    public string $square;
    public int $type;
    public int $is_education;
    public int $branch;

    const EDUCATIONAL = [0 => 'Не предназначено для образ. деятельности', 1 => 'Предназанчено для образ. деятельности'];

    public function rules()
    {
        return [
            [['id', 'is_education', 'branch', 'type'], 'integer'],
            [['globalNumber', 'name'], 'string'],
            [['globalNumber', 'name', 'is_education', 'branch', 'type', 'square'], 'safe'],
            [['square'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function __construct(
        string $globalNumber = '',
        string $name = '',
        string $square = '',
        int $type = SearchFieldHelper::EMPTY_FIELD,
        int $is_education = SearchFieldHelper::EMPTY_FIELD,
        int $branch = SearchFieldHelper::EMPTY_FIELD,
               $config = []
    )
    {
        parent::__construct($config);
        $this->globalNumber = $globalNumber;
        $this->name = $name;
        $this->square = $square;
        $this->type = $type;
        $this->is_education = $is_education;
        $this->branch = $branch;
    }

    /**
     * @param $params
     * @return void
     */
    public function loadParams($params)
    {
        if (count($params) > 1) {
            $params['SearchAuditorium']['type'] = StringFormatter::stringAsInt($params['SearchAuditorium']['type']);
            $params['SearchAuditorium']['is_education'] = StringFormatter::stringAsInt($params['SearchAuditorium']['is_education']);
            $params['SearchAuditorium']['branch'] = StringFormatter::stringAsInt($params['SearchAuditorium']['branch']);
        }

        $this->load($params);
    }

    public function search($params)
    {
        $this->loadParams($params);

        $query = AuditoriumWork::find();

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
        $dataProvider->sort->attributes['name'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['square'] = [
            'asc' => ['square' => SORT_ASC],
            'desc' => ['square' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['text'] = [
            'asc' => ['text' => SORT_ASC],
            'desc' => ['text' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['educationPretty'] = [
            'asc' => ['is_education' => SORT_ASC],
            'desc' => ['is_education' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['branchName'] = [
            'asc' => ['branch' => SORT_ASC],
            'desc' => ['branch' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['auditoriumTypeString'] = [
            'asc' => ['auditorium_type' => SORT_ASC],
            'desc' => ['auditorium_type' => SORT_DESC],
        ];
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query)
    {
        $this->filterName($query);
        $this->filterGlobalNumber($query);
        $this->filterSquare($query);
        $this->filterType($query);
        $this->filterEducation($query);
        $this->filterBranch($query);
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterName(ActiveQuery $query) {
        if (!empty($this->name)) {
            $query->andWhere(['like', 'LOWER(text)', mb_strtolower($this->name)]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterGlobalNumber(ActiveQuery $query) {
        if (!empty($this->globalNumber)) {
            $query->andWhere(['like', 'LOWER(name)', mb_strtolower($this->globalNumber)]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterSquare(ActiveQuery $query) {
        if (!empty($this->square)) {
            $query->andWhere(['square' => $this->square]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterType(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->type) && $this->type !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['auditorium_type' => $this->type]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterEducation(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->is_education) && $this->is_education !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['is_education' => $this->is_education]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterBranch(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->branch) && $this->branch !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['branch' => $this->branch]);
        }
    }
}
