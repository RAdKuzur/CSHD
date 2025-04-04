<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use common\helpers\DateFormatter;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class SearchTrainingGroup extends Model implements SearchInterfaces
{
    public string $startDateSearch;
    public string $finishDateSearch;
    public int $branch;
    public string $numberPretty;
    public string $teacher;
    public string $program;
    public int $budget;
    public int $archive;
    public int $archiveRelevance;

    public const BUDGET = [0 => 'Внебюджет', 1 => 'Бюджет'];
    public const ARCHIVE = [0 => 'Актуальные', 1 => 'Архивные'];

    public function rules()
    {
        return [
            [['id', 'branch', 'budget', 'archive'], 'integer'],
            [['numberPretty', 'teacher', 'startDateSearch', 'finishDateSearch', 'branch', 'program', 'budget', 'archive'], 'safe'],
            [['startDateSearch', 'finishDateSearch', 'number', 'teacher', 'program'], 'string'],
        ];
    }

    public function __construct(
        int $archive = SearchFieldHelper::EMPTY_FIELD,
        string $startDateSearch = '',
        string $finishDateSearch = '',
        int $branch = SearchFieldHelper::EMPTY_FIELD,
        string $numberPretty = '',
        string $teacher = '',
        string $program = '',
        int $budget = SearchFieldHelper::EMPTY_FIELD
    ) {
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->branch = $branch;
        $this->numberPretty = $numberPretty;
        $this->teacher = $teacher;
        $this->program = $program;
        $this->budget = $budget;
        $this->archive = $archive;
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
            $params['SearchTrainingGroup']['branch'] = StringFormatter::stringAsInt($params['SearchTrainingGroup']['branch']);
            $params['SearchTrainingGroup']['budget'] = StringFormatter::stringAsInt($params['SearchTrainingGroup']['budget']);
            $params['SearchTrainingGroup']['archive'] = StringFormatter::stringAsInt($params['SearchTrainingGroup']['archive']);
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

        $query = TrainingGroupWork::find()
            ->joinWith([
                'trainingProgramWork' => function ($query) {
                    $query->alias('trainingProgram');
                },
                'teachersWork' => function ($query) {
                    $query->alias('teachersGroup');
                },
                'teachersWork.teacherWork' => function ($query) {
                    $query->alias('teacherGroup');
                },
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['start_date' => SORT_DESC, 'numberPretty' => SORT_ASC]]
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
        $dataProvider->sort->attributes['numberPretty'] = [
            'asc' => ['number' => SORT_ASC],
            'desc' => ['number' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['programName'] = [
            'asc' => ['trainingProgram.name' => SORT_ASC],
            'desc' => ['trainingProgram.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['branchString'] = [
            'asc' => ['branch' => SORT_ASC],
            'desc' => ['branch' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['teachersList'] = [
            'asc' => ['teacherGroup.surname' => SORT_ASC],
            'desc' => ['teacherGroup.surname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['start_date'] = [
            'asc' => ['start_date' => SORT_ASC],
            'desc' => ['start_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['finish_date'] = [
            'asc' => ['finish_date' => SORT_ASC],
            'desc' => ['finish_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['budgetString'] = [
            'asc' => ['budget' => SORT_ASC],
            'desc' => ['budget' => SORT_DESC],
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
        $this->filterBranch($query);
        $this->filterNumber($query);
        $this->filterTeacher($query);
        $this->filterProgram($query);
        $this->filterBudget($query);
        $this->filterArchive($query);
    }

    /**
     * Фильтрация по диапазону дат
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterDate(ActiveQuery $query) {
        if (!empty($this->startDateSearch) || !empty($this->finishDateSearch)) {
            $dateFrom = $this->startDateSearch ? date('Y-m-d', strtotime($this->startDateSearch)) : DateFormatter::DEFAULT_STUDY_YEAR_START;
            $dateTo = $this->finishDateSearch ? date('Y-m-d', strtotime($this->finishDateSearch)) : date('Y-m-d');

            $query->andWhere(['or',
                ['between', 'start_date', $dateFrom, $dateTo],
                ['between', 'finish_date', $dateFrom, $dateTo],
            ]);
        }
    }

    /**
     * Поиск по отделам (месту реализации)
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterBranch(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->branch) && $this->branch !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['branch' => $this->branch]);
        }
    }

    /**
     * Поиск по названию
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterNumber(ActiveQuery $query) {
        if (!empty($this->numberPretty)) {
            $query->andFilterWhere(['like', 'LOWER(number)', $this->numberPretty]);
        }
    }

    /**
     * Поиск по преподавателям
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterTeacher(ActiveQuery $query) {
        if (!empty($this->teacher)) {
            $query->andFilterWhere(['like', 'LOWER(teacherGroup.surname)', mb_strtolower($this->teacher)]);
        }
    }

    /**
     * Поиск по программе
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterProgram(ActiveQuery $query) {
        if (!empty($this->program)) {
            $query->andFilterWhere(['like', 'LOWER(trainingProgram.name)', $this->program]);
        }
    }

    /**
     * Поиск по источнику финансирования
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterBudget(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->budget) && $this->budget !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['budget' => $this->budget]);
        }
    }

    /**
     * Поиск по статусу
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterArchive(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->archive) && $this->archive !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['archive' => $this->archive]);
        }
    }
}
