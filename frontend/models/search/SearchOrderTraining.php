<?php

namespace frontend\models\search;

use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\components\interfaces\SearchInterfaces;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\search\abstractBase\OrderSearch;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderTrainingWork;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class SearchOrderTraining extends OrderSearch implements SearchInterfaces
{
    public int $branch;
    public string $groupName;
    public string $participantName;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['branch'], 'integer'],
            [['groupName', 'participantName'], 'string'],
            [['branch', 'groupName', 'participantName'], 'safe'],
        ]);
    }

    public function __construct(
        string $orderNumber = '',
        string $orderName = '',
        string $responsibleName = '',
        string $bringName = '',
        string $executorName = '',
        string $keyWords = '',
        string $startDateSearch = '',
        string $finishDateSearch = '',
        string $groupName = '',
        string $participantName = '',
        int $branch = SearchFieldHelper::EMPTY_FIELD
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
        $this->groupName = $groupName;
        $this->participantName = $participantName;
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
            $params['SearchOrderTraining']['branch'] = StringFormatter::stringAsInt($params['SearchOrderTraining']['branch']);
        }

        $this->load($params);
    }

    /**
     * Добавление связок с другими таблицами
     *
     * @param ActiveQuery $query
     * @return ActiveQuery
     */
    private function addJoinsToQuery(ActiveQuery $query) : ActiveQuery
    {
        return $query->joinWith([
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
            },
            'orderTrainingGroupParticipantWork' => function ($query) {
                $query->alias('orderParticipant');
            },
            'orderTrainingGroupParticipantWork.trainingGroupParticipantOutWork' => function ($query) {
                $query->alias('participantOut');
            },
            'orderTrainingGroupParticipantWork.trainingGroupParticipantInWork' => function ($query) {
                $query->alias('participantIn');
            },
            'orderTrainingGroupParticipantWork.trainingGroupParticipantOutWork.participantWork' => function ($query) {
                $query->alias('foreignEventParticipantOut');
            },
            'orderTrainingGroupParticipantWork.trainingGroupParticipantInWork.participantWork' => function ($query) {
                $query->alias('foreignEventParticipantIn');
            },
            'orderTrainingGroupParticipantWork.trainingGroupParticipantOutWork.trainingGroupWork' => function ($query) {
                $query->alias('groupOut');
            },
            'orderTrainingGroupParticipantWork.trainingGroupParticipantInWork.trainingGroupWork' => function ($query) {
                $query->alias('groupIn');
            },
        ]);
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

        $query = OrderTrainingWork::find()
            ->where(['type' => DocumentOrderWork::ORDER_TRAINING]);

        $query = $this->addJoinsToQuery($query);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order_date' => SORT_DESC, 'order_number' => SORT_DESC, 'order_copy_id' => SORT_DESC, 'order_postfix' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 15,
            ],
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
        $this->filterForeignEventSurname($query);
        $this->filterGroupName($query);
        $this->filterBranch($query);
    }

    /**
     * Фильтр по названию учебной группы
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterGroupName(ActiveQuery $query)
    {
        if (!empty($this->groupName)) {
            $query->andFilterWhere([
                'or',
                ['like', 'LOWER(groupOut.number)', mb_strtolower($this->groupName)],
                ['like', 'LOWER(groupIn.number)', mb_strtolower($this->groupName)]
            ]);
        }
    }

    /**
     * Фильтр по фамилии участника деятельности фигурирующего в приказе
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterForeignEventSurname(ActiveQuery $query)
    {
        if (!empty($this->participantName)) {
            $query->andFilterWhere([
                'or',
                ['like', 'LOWER(foreignEventParticipantOut.surname)', mb_strtolower($this->participantName)],
                ['like', 'LOWER(foreignEventParticipantIn.surname)', mb_strtolower($this->participantName)]
            ]);
        }
    }

    /**
     * Фильтр по отделам
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterBranch(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->branch) && $this->branch != SearchFieldHelper::EMPTY_FIELD) {
            $nom = [];
            switch ($this->branch) {
                case BranchDictionary::QUANTORIUM:
                    $nom = NomenclatureDictionary::QUANTORIUM_NOMENCLATURES;
                    break;
                case BranchDictionary::TECHNOPARK:
                    $nom = NomenclatureDictionary::TECHNOPARK_NOMENCLATURES;
                    break;
                case BranchDictionary::CDNTT:
                    $nom = NomenclatureDictionary::CDNTT_NOMENCLATURES;
                    break;
                case BranchDictionary::MOBILE_QUANTUM:
                    $nom = NomenclatureDictionary::MOB_QUANT_NOMENCLATURES;
                    break;
                case BranchDictionary::COD:
                    $nom = NomenclatureDictionary::COD_NOMENCLATURES;
                    break;
            }
            $query->andFilterWhere(['IN', 'order_number', $nom]);
        }
    }
}