<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use common\helpers\DateFormatter;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\work\event\EventWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * SearchEvent represents the model behind the search form of `app\models\common\Event`.
 */
class SearchEvent extends Model implements SearchInterfaces
{
    public string $startDateSearch;     // дата начала поиска в диапазоне дат
    public string $finishDateSearch;    // дата окончания поиска в диапазоне дат
    public string $eventName;           // наименование мероприятия или его части
    public int $eventWay;               // формат проведения
    public int $eventLevel;             // уровень мероприятия
    public int $eventType;              // тип мероприятия
    public int $eventForm;              // форма мероприятия
    public int $eventScope;              // сфера участия
    public string $responsible;          // ответственный за мероприятие
    public int $branch;                  // отдел


    public function rules()
    {
        return [
            [['id', 'eventWay', 'eventLevel', 'eventType', 'eventForm', 'eventScope', 'branch'], 'integer'],
            [['eventName', 'responsible'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['datePeriod', 'startDateSearch', 'finishDateSearch', 'eventName', 'eventWay', 'eventLevel', 'eventType', 'eventForm'], 'safe'],
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
        int $eventLevel = SearchFieldHelper::EMPTY_FIELD,
        int $eventType = SearchFieldHelper::EMPTY_FIELD,
        int $eventWay = SearchFieldHelper::EMPTY_FIELD,
        int $eventForm = SearchFieldHelper::EMPTY_FIELD,
        int $eventScope = SearchFieldHelper::EMPTY_FIELD,
        string $responsible = '',
        int $branch = SearchFieldHelper::EMPTY_FIELD,
        $config = []
    ) {
        parent::__construct($config);
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->eventName = $eventName;
        $this->eventLevel = $eventLevel;
        $this->eventType = $eventType;
        $this->eventWay = $eventWay;
        $this->eventForm = $eventForm;
        $this->eventScope = $eventScope;
        $this->responsible = $responsible;
        $this->branch = $branch;
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
            $params['SearchEvent']['eventWay'] = StringFormatter::stringAsInt($params['SearchEvent']['eventWay']);
            $params['SearchEvent']['eventType'] = StringFormatter::stringAsInt($params['SearchEvent']['eventType']);
            $params['SearchEvent']['eventLevel'] = StringFormatter::stringAsInt($params['SearchEvent']['eventLevel']);
            $params['SearchEvent']['eventForm'] = StringFormatter::stringAsInt($params['SearchEvent']['eventForm']);
            $params['SearchEvent']['eventScope'] = StringFormatter::stringAsInt($params['SearchEvent']['eventScope']);
            $params['SearchEvent']['branch'] = StringFormatter::stringAsInt($params['SearchEvent']['branch']);
        }

        $this->load($params);
    }

    public function search($params)
    {
        $this->loadParams($params);

        $query = EventWork::find()
                ->joinWith([
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
                ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    public function sortAttributes(ActiveDataProvider $dataProvider)
    {
        $dataProvider->sort->attributes['eventName'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['datePeriod'] = [
            'asc' => ['start_date' => SORT_ASC, 'finish_date' => SORT_ASC],
            'desc' => ['start_date' => SORT_DESC, 'finish_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventLevelAndType'] = [
            'asc' => ['event_level' => SORT_ASC, 'event_type' => SORT_ASC],
            'desc' => ['event_level' => SORT_DESC, 'event_type' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['address'] = [
            'asc' => ['address' => SORT_ASC],
            'desc' => ['address' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['participantCount'] = [
            'asc' => ['participant_count' => SORT_ASC],
            'desc' => ['participant_count' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderNameRaw'] = [
            'asc' => ['orderMain.order_name' => SORT_ASC],
            'desc' => ['orderMain.order_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventWay'] = [
            'asc' => ['start_date' => SORT_ASC],
            'desc' => ['start_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['regulationRaw'] = [
            'asc' => ['regulation.name' => SORT_ASC],
            'desc' => ['regulation.name' => SORT_DESC],
        ];
    }

    public function filterQueryParams(ActiveQuery $query) {
        $this->filterDate($query);
        $this->filterName($query);
        $this->filterWay($query);
        $this->filterType($query);
        $this->filterLevel($query);
        $this->filterForm($query);
        $this->filterScope($query);
        $this->filterBranch($query);
        $this->filterResponsible($query);
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

    /**
     * Фильтрация мероприятий по формату проведения
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterWay(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->eventWay) && $this->eventWay !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['event_way' => $this->eventWay]);
        }
    }

    /**
     * Фильтрация мероприятий по типу мероприятия
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterType(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->eventType) && $this->eventType !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['event_type' => $this->eventType]);
        }
    }

    /**
     * Фильтрация мероприятий по типу мероприятия
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterLevel(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->eventLevel) && $this->eventLevel !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['event_level' => $this->eventLevel]);
        }
    }

    /**
     * Фильтрация мероприятий по форме мероприятия
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterForm(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->eventForm) && $this->eventForm !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['event_form' => $this->eventForm]);
        }
    }

    /**
     * Фильтрация сфере участия
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterScope(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->eventScope) && $this->eventScope !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['participation_scope' => $this->eventScope]);
        }
    }

    /**
     * Фильтрация по отделам, которые проводят мероприятие
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterBranch(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->branch) && $this->branch !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['branch' => $this->branch]);
        }
    }

    /**
     * Фильтрация по ответственным
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterResponsible(ActiveQuery $query) {
        if (!empty($this->responsible)) {
            $responsibleLOWER = mb_strtolower($this->responsible);
            $query->andFilterWhere(['or',
                ['like', 'LOWER(responsibleOne.surname)', $responsibleLOWER],
                ['like', 'LOWER(responsibleTwo.surname)', $responsibleLOWER]
            ]);
        }
    }
}
