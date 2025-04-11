<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\work\event\ForeignEventWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class SearchForeignEvent extends Model implements SearchInterfaces
{
    public string $eventLevel;             // уровень мероприятия
    public string $startDateSearch;     // дата начала поиска в диапазоне дат
    public string $finishDateSearch;    // дата окончания поиска в диапазоне дат
    public string $nameParticipant;     // фамилия участника
    public string $nameTeacher;         // фамилия педагога
    public int $branch;                 // отдел
    public string $organizerName;       // организатор
    public string $eventName;           // наименование мероприятия
    public string $city;                // город
    public string $eventWay;               // формат проведения
    public string $keyWord;             // ключевые слова

    public function rules()
    {
        return [
            [['id', 'eventWay', 'eventLevel', 'branch'], 'integer'],
            [['nameParticipant', 'nameTeacher', 'organizerName', 'eventName', 'city', 'keyWord'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['eventWay', 'eventLevel', 'branch', 'startDateSearch', 'finishDateSearch', 'nameParticipant', 'nameTeacher', 'organizerName', 'eventName', 'city', 'keyWord'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function __construct(
        string $startDateSearch = '',
        string $finishDateSearch = '',
        string $eventName = '',
        string $nameParticipant = '',
        string $nameTeacher = '',
        string $eventLevel = '',
        string $eventWay = '',
        int $branch = SearchFieldHelper::EMPTY_FIELD,
        string $organizerName = '',
        string $city = '',
        string $keyWord = '',
        $config = []
    ) {
        parent::__construct($config);
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->eventName = $eventName;
        $this->eventLevel = $eventLevel;
        $this->eventWay = $eventWay;
        $this->branch = $branch;
        $this->nameParticipant = $nameParticipant;
        $this->nameTeacher = $nameTeacher;
        $this->organizerName = $organizerName;
        $this->city = $city;
        $this->keyWord = $keyWord;
    }

    /**
     * Определение параметров загрузки данных
     *
     * @param $params
     * @return void
     */
    public function loadParams($params)
    {
        if (count($params) > 1) {
            $params['SearchForeignEvent']['branch'] = StringFormatter::stringAsInt($params['SearchForeignEvent']['branch']);
        }

        $this->load($params);
    }

    public function search($params)
    {
        $this->loadParams($params);

        $query = ForeignEventWork::find()
            /*->joinWith([
                'documentOrderWork' => function ($query) {
                    $query->alias('orderMain');
                },
                'regulationWork' => function ($query) {
                    $query->alias('regulation');
                },
                'scopesWork' => function ($query) {
                    $query->alias('scopes');
                },
                'eventBranchWorks' => function ($query) {
                    $query->alias('branches');
                },
                'responsible1Work' => function ($query) {
                    $query->alias('resp1');
                },
                'responsible1Work.peopleWork' => function ($query) {
                    $query->alias('responsibleOne');
                },
                'responsible2Work' => function ($query) {
                    $query->alias('resp2');
                },
                'responsible2Work.peopleWork' => function ($query) {
                    $query->alias('responsibleTwo');
                },
            ])*/;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    public function sortAttributes(ActiveDataProvider $dataProvider)
    {
        $dataProvider->sort->attributes['name'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['period'] = [
            'asc' => ['begin_date' => SORT_ASC, 'end_date' => SORT_ASC],
            'desc' => ['begin_date' => SORT_DESC, 'end_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['city'] = [
            'asc' => ['city' => SORT_ASC],
            'desc' => ['city' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventWayString'] = [
            'asc' => ['format' => SORT_ASC],
            'desc' => ['format' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventLevelString'] = [
            'asc' => ['level' => SORT_ASC],
            'desc' => ['level' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['teachers'] = [
            /*'asc' => ['orderMain.order_name' => SORT_ASC],
            'desc' => ['orderMain.order_name' => SORT_DESC],*/
        ];

        $dataProvider->sort->attributes['participantCount'] = [
            /*'asc' => ['start_date' => SORT_ASC],
            'desc' => ['start_date' => SORT_DESC],*/
        ];

        $dataProvider->sort->attributes['winners'] = [
            /*'asc' => ['regulation.name' => SORT_ASC],
            'desc' => ['regulation.name' => SORT_DESC],*/
        ];

        $dataProvider->sort->attributes['prizes'] = [
            /*'asc' => ['regulation.name' => SORT_ASC],
            'desc' => ['regulation.name' => SORT_DESC],*/
        ];
    }

    public function filterQueryParams(ActiveQuery $query) {
        /*$this->filterDate($query);
        $this->filterName($query);
        $this->filterWay($query);
        $this->filterType($query);
        $this->filterLevel($query);
        $this->filterForm($query);
        $this->filterScope($query);
        $this->filterBranch($query);
        $this->filterResponsible($query);*/
    }

    /**
     * Фильтрация мероприятий по диапазону дат
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterDate(ActiveQuery $query) {
        if (!empty($this->startDateSearch) || !empty($this->finishDateSearch))
        {
            $dateFrom = $this->startDateSearch ? date('Y-m-d', strtotime($this->startDateSearch)) : DateFormatter::DEFAULT_STUDY_YEAR_START;
            $dateTo =  $this->finishDateSearch ? date('Y-m-d', strtotime($this->finishDateSearch)) : date('Y-m-d');

            $query->andWhere(['or',
                ['between', 'start_date', $dateFrom, $dateTo],
                ['between', 'finish_date', $dateFrom, $dateTo],
            ]);
        }
    }

    /**
     * Фильтрация мероприятий по названию
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterName(ActiveQuery $query) {
        if (!empty($this->eventName)) {
            $query->andWhere(['like', 'LOWER(event.name)', mb_strtolower($this->eventName)]);
        }
    }
}
