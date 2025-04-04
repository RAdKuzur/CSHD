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


class SearchRegulation extends RegulationSearch implements SearchInterfaces
{
    public int $numberBoard;    // Номер совета

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['numberBoard'], 'integer'],
        ]);
    }

    public function __construct(
        string $startDateSearch = '',
        string $finishDateSearch = '',
        string $nameRegulation = '',
        string $orderName = '',
        int $status = SearchFieldHelper::EMPTY_FIELD,
        int $numberBoard = SearchFieldHelper::EMPTY_FIELD
    ) {
        parent::__construct(
            $startDateSearch,
            $finishDateSearch,
            $nameRegulation,
            $orderName,
            $status
        );
        $this->numberBoard = $numberBoard;
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
            $params['SearchRegulation']['numberBoard'] = StringFormatter::stringAsInt($params['SearchRegulation']['numberBoard']);
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
        $this->loadParams($params);

        $query = RegulationWork::find()
            ->joinWith([
                'documentOrderWork' => function ($query) {
                    $query->alias('orderMain');
                },
            ])
            ->where(['regulation_type' => RegulationTypeDictionary::TYPE_REGULATION]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    /**
     * Кастомизированная сортировка по полям таблицы, с учетом родительской сортировки
     *
     * @param ActiveDataProvider $dataProvider
     * @return void
     */
    public function sortAttributes(ActiveDataProvider $dataProvider)
    {
        parent::sortAttributes($dataProvider);

        $dataProvider->sort->attributes['ped_council_number'] = [
            'asc' => ['state' => SORT_ASC],
            'desc' => ['state' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['ped_council_date'] = [
            'asc' => ['state' => SORT_ASC],
            'desc' => ['state' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['par_council_number'] = [
            'asc' => ['state' => SORT_ASC],
            'desc' => ['state' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['par_council_date'] = [
            'asc' => ['state' => SORT_ASC],
            'desc' => ['state' => SORT_DESC],
        ];
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

        $this->filterNumberBoard($query);
    }

    /**
     * Фильтрация по номеру совета
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterNumberBoard(ActiveQuery $query)
    {
        if (!empty($this->numberBoard)) {
            $query->andFilterWhere(['=', 'ped_council_number', $this->numberBoard]);
        }
    }

}
