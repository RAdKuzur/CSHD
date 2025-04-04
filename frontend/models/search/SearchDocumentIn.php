<?php

namespace frontend\models\search;

use common\components\dictionaries\base\DocumentStatusDictionary;
use common\components\interfaces\SearchInterfaces;
use common\helpers\DateFormatter;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\search\abstractBase\DocumentSearch;
use frontend\models\work\document_in_out\DocumentInWork;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class SearchDocumentIn extends DocumentSearch implements SearchInterfaces
{
    public string $localDate;              // дата поступления документа (используется для сортировки)
    public string $realDate;               // регистрационная дата документа (используется для сортировки)


    public function rules()
    {
        return array_merge(parent::rules(), [
            [['local_number'], 'integer'],
            [['realNumber'], 'string'],
            [['localDate', 'realDate'], 'safe'],
        ]);
    }

    public function __construct(
        string $fullNumber = '',
        string $companyName = '',
        int $sendMethod = SearchFieldHelper::EMPTY_FIELD,
        string $documentTheme = '',
        string $startDateSearch = '',
        string $finishDateSearch = '',
        string $executorName = '',
        int $status = SearchFieldHelper::EMPTY_FIELD,
        string $keyWords = '',
        string $correspondentName = '',
        string $number = '',
        string $localDate = '',
        string $realDate = ''
    ) {
        parent::__construct(
            $fullNumber,
            $companyName,
            $sendMethod,
            $documentTheme,
            $startDateSearch,
            $finishDateSearch,
            $executorName,
            $status,
            $keyWords,
            $correspondentName,
            $number
        );
        $this->localDate = $localDate;
        $this->realDate = $realDate;
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
            $params['SearchDocumentIn']['sendMethod'] = StringFormatter::stringAsInt($params['SearchDocumentIn']['sendMethod']);
            $params['SearchDocumentIn']['status'] = StringFormatter::stringAsInt($params['SearchDocumentIn']['status']);
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

        $query = DocumentInWork::find()
            ->joinWith([
                'companyWork' => function ($query) {
                    $query->alias('company');
                },
                'correspondentWork' => function ($query) {
                    $query->alias('correspondent');
                },
                'correspondentWork.peopleWork' => function ($query) {
                    $query->alias('correspondentPeople');
                },
                'inOutDocumentWork.responsibleWork' => function ($query) {
                    $query->alias('responsible');
                },
                'inOutDocumentWork.responsibleWork.peopleWork' => function ($query) {
                    $query->alias('responsiblePeople');
                }
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['local_date' => SORT_DESC, 'local_number' => SORT_DESC, 'local_postfix' => SORT_DESC]]
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
    public function sortAttributes(ActiveDataProvider $dataProvider) {
        parent::sortAttributes($dataProvider);

        $dataProvider->sort->attributes['localDate'] = [
            'asc' => ['local_date' => SORT_ASC],
            'desc' => ['local_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['realDate'] = [
            'asc' => ['real_date' => SORT_ASC],
            'desc' => ['real_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['realNumber'] = [
            'asc' => ['real_number' => SORT_ASC],
            'desc' => ['real_number' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['needAnswer'] = [
            'asc' => ['need_answer' => SORT_DESC],
            'desc' => ['need_answer' => SORT_ASC],
        ];
    }


    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query) {
        $this->filterStatus($query);
        $this->filterDate($query);
        $this->filterNumber($query);
        $this->filterExecutorName($query);
        $this->filterAbstractQueryParams($query, $this->documentTheme, $this->keyWords, $this->sendMethod, $this->correspondentName);
    }


    /**
     * Фильтрует по статусу документа
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterStatus(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->status) && $this->status != SearchFieldHelper::EMPTY_FIELD) {
            $statusConditions = [
                DocumentStatusDictionary::CURRENT => ['>=', 'local_date', date('Y') . '-01-01'],
                DocumentStatusDictionary::ARCHIVE => ['<=', 'local_date', date('Y-m-d')],
                DocumentStatusDictionary::EXPIRED => [
                    'AND',
                    ['<', 'date', date('Y-m-d')],
                    ['IS', 'document_out_id', null]
                ],
                DocumentStatusDictionary::NEEDANSWER => ['=', 'need_answer', 1],
                DocumentStatusDictionary::RESERVED => ['like', 'LOWER(document_theme)', 'РЕЗЕРВ'],
            ];
            $query->andWhere($statusConditions[$this->status]);
        }
    }

    /**
     * Фильтрация документов по диапазону дат
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterDate(ActiveQuery $query) {
        if (!empty($this->startDateSearch) || !empty($this->finishDateSearch)) {
            $dateFrom = $this->startDateSearch ? date('Y-m-d', strtotime($this->startDateSearch)) : DateFormatter::DEFAULT_STUDY_YEAR_START;
            $dateTo =  $this->finishDateSearch ? date('Y-m-d', strtotime($this->finishDateSearch)) : date('Y-m-d');

            $query->andWhere(['or',
                ['between', 'local_date', $dateFrom, $dateTo],
                ['between', 'real_date', $dateFrom, $dateTo],
            ]);
        }
    }

    /**
     * Фильтрация документа по заданному номеру (реальному или локальному)
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterNumber(ActiveQuery $query) {
        if (!empty($this->number)) {
            $query->andFilterWhere(['or',
                ['like', 'real_number', $this->number],
                ['like', "CONCAT(local_number, '/', local_postfix)", $this->number],
            ]);
        }
    }

    /**
     * Фильтрует по исполнителю документа
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterExecutorName(ActiveQuery $query) {
        if (!empty($this->executorName)) {
            $query->andFilterWhere(['like', 'LOWER(responsiblePeople.firstname)', mb_strtolower($this->executorName)]);
        }
    }
}
