<?php

namespace frontend\models\search\abstractBase;

use common\helpers\DateFormatter;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\work\regulation\RegulationWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class RegulationSearch extends Model
{
    public string $startDateSearch;    // стартовая дата поиска положений
    public string $finishDateSearch;   // конечная дата поиска положений
    public string $nameRegulation;     // краткое или полное наименование положения
    public string $orderName;          // добавленный к положению приказ
    public int $status;                // статус положения

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nameRegulation', 'orderName'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['startDateSearch', 'finishDateSearch', 'status'], 'safe'],
        ];
    }

    public function __construct(
        string $startDateSearch = '',
        string $finishDateSearch = '',
        string $nameRegulation = '',
        string $orderName = '',
        int $status = SearchFieldHelper::EMPTY_FIELD
    ) {
        parent::__construct();
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->nameRegulation = $nameRegulation;
        $this->orderName = $orderName;
        $this->status = $status;
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Сортировка атрибутов запроса
     *
     * @param ActiveDataProvider $dataProvider
     * @return void
     */
    public function sortAttributes(ActiveDataProvider $dataProvider) {
        $dataProvider->sort->attributes['date'] = [
            'asc' => ['date' => SORT_ASC],
            'desc' => ['date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['nameRegulation'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderName'] = [
            'asc' => ['orderMain.order_name' => SORT_ASC],
            'desc' => ['orderMain.order_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['state'] = [
            'asc' => ['state' => SORT_ASC],
            'desc' => ['state' => SORT_DESC],
        ];
    }

    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @param string $startDateSearch
     * @param string $finishDateSearch
     * @param string $nameRegulation
     * @param string $orderName
     * @param int $status
     * @return void
     */
    public function filterAbstractQueryParams(ActiveQuery $query, string $startDateSearch, string $finishDateSearch, string $nameRegulation, string $orderName, int $status) {
        $this->filterDate($query, $startDateSearch, $finishDateSearch);
        $this->filterName($query, $nameRegulation);
        $this->filterOrderName($query, $orderName);
        $this->filterStatus($query, $status);
    }

    /**
     * Фильтрация документов по диапазону дат
     *
     * @param ActiveQuery $query
     * @param string $startDateSearch
     * @param string $finishDateSearch
     * @return void
     */
    private function filterDate(ActiveQuery $query, string $startDateSearch, string $finishDateSearch) {
        if (!empty($startDateSearch) || !empty($finishDateSearch))
        {
            $dateFrom = $startDateSearch ? date('Y-m-d', strtotime($startDateSearch)) : DateFormatter::DEFAULT_STUDY_YEAR_START;
            $dateTo =  $finishDateSearch ? date('Y-m-d', strtotime($finishDateSearch)) : date('Y-m-d');

            $query->andWhere(['between', 'date', $dateFrom, $dateTo]);
        }
    }

    /**
     * Фильтрация положений по наименованию или крткому наименованию
     *
     * @param ActiveQuery $query
     * @param string $nameRegulation
     * @return void
     */
    private function filterName(ActiveQuery $query, string $nameRegulation) {
        if (!empty($nameRegulation)) {
            $query->andFilterWhere(['or',
                ['like', 'LOWER(name)', mb_strtolower($nameRegulation)],
                ['like', 'LOWER(short_name)', mb_strtolower($nameRegulation)],
            ]);
        }
    }

    /**
     * Фильтрация положений по наименованию приказа
     *
     * @param ActiveQuery $query
     * @param string $orderName
     * @return void
     */
    private function filterOrderName(ActiveQuery $query, string $orderName) {
        if (!empty($orderName)) {
            $query->andFilterWhere(['or',
                ['like', 'LOWER(order_name)', mb_strtolower($orderName)],
                ['like', "CONCAT(order_number, '/', order_postfix)", $orderName],
            ]);
        }
    }

    /**
     * Фильтрация статуса: актуально/утратило силу
     *
     * @param ActiveQuery $query
     * @param int $status
     * @return void
     */
    private function filterStatus(ActiveQuery $query, int $status) {
        if (!StringFormatter::isEmpty($status) && $status !== RegulationWork::STATE_ACTIVE)
        {
            $query->andFilterWhere(['like', 'regulation.state', $status]);
        }
    }
}