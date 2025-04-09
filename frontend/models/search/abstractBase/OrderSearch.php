<?php

namespace frontend\models\search\abstractBase;

use common\helpers\DateFormatter;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class OrderSearch extends Model
{
    public string $orderNumber;         // номер документа
    public string $orderName;           // название приказа
    public string $responsibleName;     // ответственный
    public string $bringName;           // проект вносит
    public string $executorName;        // кто исполняет
    public string $keyWords;
    public string $startDateSearch;    // стартовая дата поиска документов
    public string $finishDateSearch;   // конечная дата поиска документов


    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['orderNumber', 'orderName', 'signedName', 'bringName', 'executorName', 'keyWords'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['orderNumber', 'orderName', 'responsibleName', 'bringName', 'executorName', 'keyWords', 'startDateSearch', 'finishDateSearch'], 'safe'],
        ];
    }

    public function __construct(
        string $orderNumber = '',
        string $orderName = '',
        string $responsibleName = '',
        string $bringName = '',
        string $executorName = '',
        string $keyWords = '',
        string $startDateSearch = '',
        string $finishDateSearch = ''
    ) {
        parent::__construct();
        $this->orderNumber = $orderNumber;
        $this->orderName = $orderName;
        $this->responsibleName = $responsibleName;
        $this->bringName = $bringName;
        $this->executorName = $executorName;
        $this->keyWords = $keyWords;
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
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
        $dataProvider->sort->attributes['number'] = [
            'asc' => ['order_number' => SORT_ASC, 'order_copy_id' => SORT_ASC, 'order_postfix' => SORT_ASC],
            'desc' => ['order_number' => SORT_DESC, 'order_copy_id' => SORT_DESC, 'order_postfix' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderName'] = [
            'asc' => ['order_name' => SORT_ASC],
            'desc' => ['order_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['bringName'] = [
            'asc' => ['bringPeople.surname' => SORT_ASC, 'bringPeople.firstname' => SORT_ASC],
            'desc' => ['bringPeople.surname' => SORT_DESC, 'bringPeople.firstname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['executorName'] = [
            'asc' => ['executorPeople.surname' => SORT_ASC, 'executorPeople.firstname' => SORT_ASC],
            'desc' => ['executorPeople.surname' => SORT_DESC, 'executorPeople.firstname' => SORT_DESC],
        ];
    }

    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @param string $orderNumber
     * @param string $orderName
     * @param string $responsibleName
     * @param string $executorName
     * @param string $keyWords
     * @param string $startDateSearch
     * @param string $finishDateSearch
     * @return void
     */
    public function filterAbstractQueryParams(
        ActiveQuery $query,
        string $orderNumber,
        string $orderName,
        string $responsibleName,
        string $executorName,
        string $bringName,
        string $keyWords,
        string $startDateSearch,
        string $finishDateSearch) {
        $this->filterNumber($query, $orderNumber);
        $this->filterOrderName($query, $orderName);
        $this->filterResponsibleName($query, $responsibleName);
        $this->filterExecutorName($query, $executorName);
        $this->filterBringName($query, $bringName);
        $this->filterDate($query, $startDateSearch, $finishDateSearch);
        $this->filterKeyWords($query, $keyWords);
    }

    /**
     * Фильтрация документа по заданному номеру
     *
     * @param ActiveQuery $query
     * @param string $orderNumber
     * @return void
     */
    private function filterNumber(ActiveQuery $query, string $orderNumber) {
        if (!empty($this->number)) {
            $query->andFilterWhere(['or',
                ['like', 'order_number', $orderNumber],
                ['like', 'order_copy_id', $orderNumber],
                ['like', 'order_postfix', $orderNumber],
            ]);
        }
    }

    /**
     * Фильтрация по названию приказа
     *
     * @param ActiveQuery $query
     * @param string $orderName
     * @return void
     */
    private function filterOrderName(ActiveQuery $query, string $orderName) {
        if (!empty($orderName)) {
            $query->andFilterWhere(['like', 'LOWER(order_name)', mb_strtolower($orderName)]);
        }
    }

    /**
     * Фильтрует по тому кто вносит проект
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterExecutorName(ActiveQuery $query, string $executorName) {
        if (!empty($executorName)) {
            $query->andFilterWhere(['or',
                ['like', 'LOWER(executorPeople.surname)', mb_strtolower($executorName)],
                ['like', 'LOWER(executorPeople.firstname)', mb_strtolower($executorName)]
            ]);
        }
    }

    /**
     * Фильтрует по тому кто исполняет
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterBringName(ActiveQuery $query, string $bringName) {
        if (!empty($bringName)) {
            $query->andFilterWhere(['or',
                ['like', 'LOWER(bringPeople.surname)', mb_strtolower($bringName)],
                ['like', 'LOWER(bringPeople.firstname)', mb_strtolower($bringName)]
            ]);
        }
    }

    /**
     * Фильтрует по ответственным
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterResponsibleName(ActiveQuery $query, string $responsibleName) {
        if (!empty($responsibleName)) {
            $query->andFilterWhere(['or',
                ['like', 'LOWER(responsiblePeople.surname)', mb_strtolower($responsibleName)],
                ['like', 'LOWER(responsiblePeople.firstname)', mb_strtolower($responsibleName)]
            ]);
        }
    }

    /**
     * Фильтрация приказов по диапазону дат
     *
     * @param ActiveQuery $query
     * @param string $startDateSearch
     * @param string $finishDateSearch
     * @return void
     */
    private function filterDate(ActiveQuery $query, string $startDateSearch, string $finishDateSearch) {
        if (!empty($startDateSearch) || !empty($this->finishDateSearch)) {
            $dateFrom = $startDateSearch ? date('Y-m-d', strtotime($startDateSearch)) : DateFormatter::DEFAULT_STUDY_YEAR_START;
            $dateTo =  $finishDateSearch ? date('Y-m-d', strtotime($finishDateSearch)) : date('Y-m-d');

            $query->andWhere(['or',
                ['between', 'local_date', $dateFrom, $dateTo],
                ['between', 'real_date', $dateFrom, $dateTo],
            ]);
        }
    }

    /**
     * Фильтрует по ключевым словам
     *
     * @param ActiveQuery $query
     * @param string $keyWords
     * @return void
     */
    private function filterKeyWords(ActiveQuery $query, string $keyWords) {
        if (!empty($keyWords)) {
            $query->andFilterWhere(['like', 'LOWER(key_words)', mb_strtolower($keyWords)]);
        }
    }
}