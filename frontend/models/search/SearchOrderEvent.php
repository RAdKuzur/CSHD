<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use frontend\models\search\abstractBase\OrderSearch;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderEventWork;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class SearchOrderEvent extends OrderSearch implements SearchInterfaces
{
    public function rules()
    {
        return parent::rules();
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
        parent::__construct(
            $orderNumber,
            $orderName,
            $responsibleName,
            $bringName,
            $executorName,
            $keyWords,
            $startDateSearch,
            $finishDateSearch
        );
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
     * Создает экземпляр DataProvider с учетом поискового запроса (фильтров или сортировки)
     *
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->loadParams($params);

        $query = OrderEventWork::find()
            ->where(['type' => DocumentOrderWork::ORDER_EVENT])
            ->joinWith([
                'bringWork' => function ($query) {
                    $query->alias('bring');
                },
                'bringWork.peopleWork' => function ($query) {
                    $query->alias('bringPeople');
                },
                'executorWork' => function ($query) {
                    $query->alias('executor');
                },
                'executorWork.peopleWork' => function ($query) {
                    $query->alias('executorPeople');
                },
                'orderPeopleWorks' => function ($query) {
                    $query->alias('orderPeople');
                },
                'orderPeopleWorks.peopleStampWork' => function ($query) {
                    $query->alias('orderStamp');
                },
                'orderPeopleWorks.peopleStampWork.peopleWork' => function ($query) {
                    $query->alias('responsiblePeople');
                }
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order_date' => SORT_DESC, 'order_number' => SORT_DESC, 'order_copy_id' => SORT_DESC, 'order_postfix' => SORT_DESC]]
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
    public function sortAttributes(ActiveDataProvider $dataProvider) {
        parent::sortAttributes($dataProvider);
    }

    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query) {
        $this->filterAbstractQueryParams(
            $query,
            $this->orderNumber,
            $this->orderName,
            $this->responsibleName,
            $this->executorName,
            $this->bringName,
            $this->keyWords,
            $this->startDateSearch,
            $this->finishDateSearch);
    }
}