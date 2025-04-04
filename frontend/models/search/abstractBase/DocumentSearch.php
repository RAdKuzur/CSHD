<?php

namespace frontend\models\search\abstractBase;

use common\components\dictionaries\base\DocumentStatusDictionary;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class DocumentSearch extends Model
{
    public string $fullNumber;         // составной номер документа (может содержать символ '/' )
    public string $companyName;        // организация - отправитель или получатель письма
    public int $sendMethod;     // способ отправки или получения письма
    public string $documentTheme;      // тема документа
    public string $startDateSearch;    // стартовая дата поиска документов
    public string $finishDateSearch;   // конечная дата поиска документов
    public string $executorName;       // исполнитель письма
    public int $status;             // статус документа (архивное, требуется ответ, отвеченное, и т.д.)
    public string $keyWords;           // ключевые слова
    public string $correspondentName;  // корреспондент (отправитель) фио или организация
    public string $number;             // номер документа (регистрационный или присвоенный нами)

    public function rules()
    {
        return [
            [['id', 'positionId', 'companyId', 'signedId', 'getId', 'creatorId'], 'integer'],
            [['fullNumber', 'keyWords'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['documentTheme', 'companyName', 'sendMethod', 'executorName', 'status', 'correspondentName', 'number'], 'safe'],
        ];
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
        string $number = ''
    ) {
        parent::__construct();
        $this->fullNumber = $fullNumber;
        $this->companyName = $companyName;
        $this->sendMethod = $sendMethod;
        $this->documentTheme = $documentTheme;
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->executorName = $executorName;
        $this->status = $status == null ? DocumentStatusDictionary::CURRENT : $status;
        $this->keyWords = $keyWords;
        $this->correspondentName = $correspondentName;
        $this->number = $number;
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
        $dataProvider->sort->attributes['fullNumber'] = [
            'asc' => ['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC],
            'desc' => ['local_number' => SORT_DESC, 'local_postfix' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['companyName'] = [
            'asc' => ['company.name' => SORT_ASC],
            'desc' => ['company.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['documentTheme'] = [
            'asc' => ['document_theme' => SORT_ASC],
            'desc' => ['document_theme' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sendMethod'] = [
            'asc' => ['send_method' => SORT_ASC],
            'desc' => ['send_method' => SORT_DESC],
        ];
    }

    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @param string $documentTheme
     * @param string $keyWords
     * @param int $sendMethod
     * @param string $correspondentName
     * @return void
     */
    public function filterAbstractQueryParams(ActiveQuery $query, string $documentTheme, string $keyWords, int $sendMethod, string $correspondentName) {
        $this->filterTheme($query, $documentTheme);
        $this->filterKeyWords($query, $keyWords);
        $this->filterSendMethod($query, $sendMethod);
        $this->filterCorrespondentName($query, $correspondentName);
    }

    /**
     * Фильтрует по теме документа
     *
     * @param ActiveQuery $query
     * @param string $documentTheme
     * @return void
     */
    private function filterTheme(ActiveQuery $query, string $documentTheme) {
        if (!empty($documentTheme)) {
            $query->andFilterWhere(['like', 'LOWER(document_theme)', mb_strtolower($documentTheme)]);
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

    /**
     * Фильтрует по методу получения письма
     *
     * @param ActiveQuery $query
     * @param int $sendMethod
     * @return void
     */
    private function filterSendMethod(ActiveQuery $query, int $sendMethod) {
        if (!StringFormatter::isEmpty($sendMethod) && $sendMethod !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['send_method' => $sendMethod]);
        }
    }

    /**
     * Фильтрация документов любому из полей "Ф И О" корреспондента
     *
     * @param ActiveQuery $query
     * @param string $correspondentName
     * @return void
     */
    private function filterCorrespondentName(ActiveQuery $query, string $correspondentName) {
        if (!empty($correspondentName)) {
            $lowerCorrespondentName = mb_strtolower($correspondentName);
            $query->andFilterWhere(['or',
                ['like', 'LOWER(company.name)', $lowerCorrespondentName],
                ['like', 'LOWER(company.short_name)', $lowerCorrespondentName],
                ['like', 'LOWER(correspondentPeople.firstname)', $lowerCorrespondentName],
            ]);
        }
    }
}