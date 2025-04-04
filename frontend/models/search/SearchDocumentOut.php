<?php

namespace frontend\models\search;

use common\components\dictionaries\base\DocumentStatusDictionary;
use common\components\interfaces\SearchInterfaces;
use common\helpers\DateFormatter;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\search\abstractBase\DocumentSearch;
use frontend\models\work\document_in_out\DocumentOutWork;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class SearchDocumentOut extends DocumentSearch implements SearchInterfaces
{
    public string $documentDate;       // дата документа
    public string $sentDate;           // дата отправки документа

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['documentDate', 'sentDate'], 'safe'],
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
        string $documentDate = '',
        string $sentDate = ''
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
        $this->documentDate = $documentDate;
        $this->sentDate = $sentDate;
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
            $params['SearchDocumentOut']['sendMethod'] = StringFormatter::stringAsInt($params['SearchDocumentOut']['sendMethod']);
            $params['SearchDocumentOut']['status'] = StringFormatter::stringAsInt($params['SearchDocumentOut']['status']);
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
        $query = DocumentOutWork::find()
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
                },
                'executorWork' => function ($query) {
                    $query->alias('executor');
                },
                'executorWork.peopleWork' => function ($query) {
                    $query->alias('executorPeople');
                },
                'signedWork' => function ($query) {
                    $query->alias('signed');
                },
                'signedWork.peopleWork' => function ($query) {
                    $query->alias('signedPeople');
                }
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['document_date' => SORT_DESC, 'document_number' => SORT_DESC, 'document_postfix' => SORT_DESC]]
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

        $dataProvider->sort->attributes['documentDate'] = [
            'asc' => ['document_date' => SORT_ASC],
            'desc' => ['document_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['executorName'] = [
            'asc' => ['people.firstname' => SORT_ASC],
            'desc' => ['people.firstname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sentDate'] = [
            'asc' => ['send_date' => SORT_ASC],
            'desc' => ['send_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['isAnswer'] = [
            'asc' => ['is_answer' => SORT_DESC],
            'desc' => ['is_answer' => SORT_ASC],
        ];
    }


    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query) {
        $this->filterDate($query);
        $this->filterNumber($query);
        $this->filterStatus($query);
        $this->filterExecutorName($query);
        $this->filterAbstractQueryParams($query, $this->documentTheme, $this->keyWords, $this->sendMethod, $this->correspondentName);
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

            $query->andWhere([
                'or',
                ['between', 'document_date', $dateFrom, $dateTo],
                ['between', 'sent_date', $dateFrom, $dateTo],
            ]);
        }
    }

    /**
     * Фильтрация документа по заданному номеру
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterNumber(ActiveQuery $query) {
        if (!empty($this->number)) {
            $query->andFilterWhere(['like', "CONCAT(document_number, '/', document_postfix)", $this->number]);
        }
    }

    /**
     * Фильтрует по статусу документа
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterStatus(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->status) && $this->status !== SearchFieldHelper::EMPTY_FIELD) {
            $statusConditions = [
                DocumentStatusDictionary::CURRENT => ['>=', 'document_date', date('Y') . '-01-01'],
                DocumentStatusDictionary::ARCHIVE => ['<=', 'document_date', date('Y-m-d')],
                DocumentStatusDictionary::RESERVED => ['like', 'LOWER(document_theme)', 'РЕЗЕРВ'],
                DocumentStatusDictionary::ANSWER => ['IS NOT', 'document_out_id', null],
            ];
            $query->andWhere($statusConditions[$this->status]);
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
            $query->andFilterWhere([
                'OR',
                ['like', 'LOWER(executorPeople.firstname)', mb_strtolower($this->executorName)],
                ['like', 'LOWER(signedPeople.firstname)', mb_strtolower($this->executorName)],
            ]);
        }
    }
}