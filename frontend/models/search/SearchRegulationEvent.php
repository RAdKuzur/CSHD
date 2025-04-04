<?php

namespace frontend\models\search;

use common\components\dictionaries\base\RegulationTypeDictionary;
use common\components\interfaces\SearchInterfaces;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\search\abstractBase\RegulationSearch;
use frontend\models\work\regulation\RegulationWork;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;


class SearchRegulationEvent extends RegulationSearch implements SearchInterfaces
{
    public function rules()
    {
        return parent::rules();
    }

    public function __construct(
        string $startDateSearch = '',
        string $finishDateSearch = '',
        string $nameRegulation = '',
        string $orderName = '',
        int $status = SearchFieldHelper::EMPTY_FIELD
    ) {
        parent::__construct($startDateSearch, $finishDateSearch, $nameRegulation, $orderName, $status);
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
            $params['SearchRegulation']['status'] = StringFormatter::stringAsInt($params['SearchRegulation']['status']);
        }

        $this->load($params);
    }

    /**
     * Создает экземпляр DataProvider с учетом поискового запроса (фильтров или сортировки)
     *
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $query = RegulationWork::find()
                ->joinWith([
                    'documentOrderWork' => function ($query) {
                        $query->alias('orderMain');
                    },
                ])
                ->where(['regulation_type' => RegulationTypeDictionary::TYPE_EVENT]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    /**
     * Сортировка по полям таблицы
     *
     * @param ActiveDataProvider $dataProvider
     * @return void
     */
    public function sortAttributes(ActiveDataProvider $dataProvider)
    {
        parent::sortAttributes($dataProvider);
    }

    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query)
    {
        parent::filterAbstractQueryParams($query, $this->startDateSearch, $this->finishDateSearch, $this->nameRegulation, $this->orderName, $this->status);
    }
}
