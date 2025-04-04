<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use common\helpers\DateFormatter;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use common\Model;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use function PHPUnit\Framework\isEmpty;


class SearchTrainingProgram extends Model implements SearchInterfaces
{
    public string $startDateSearch;
    public string $finishDateSearch;
    public string $programName;
    public string $authorSearch;
    public int $branchSearch;
    public int $focusSearch;
    public int $allowSearch;
    public int $levelSearch;
    public int $actual;
    public int $actualRelevance;

    public const ACTUAL = [0 => 'Не актуальные программы', 1 => 'Актуальные программы'];

    public function rules()
    {
        return [
            [['branchSearch', 'focusSearch', 'allowSearch', 'levelSearch', 'actualRelevance'], 'integer'],
            [['programName', 'authorSearch'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['name', 'startDateSearch', 'finishDateSearch', 'programName', 'authorSearch', 'branchSearch', 'focusSearch', 'allowSearch', 'levelSearch', 'actualRelevance'], 'safe'],
        ];
    }

    public function __construct(
        int $activeValidators = SearchFieldHelper::EMPTY_FIELD,
        string $startDateSearch = '',
        string $finishDateSearch = '',
        string $programName = '',
        string $authorSearch = '',
        int $branchSearch = SearchFieldHelper::EMPTY_FIELD,
        int $focusSearch = SearchFieldHelper::EMPTY_FIELD,
        int $allowSearch = SearchFieldHelper::EMPTY_FIELD,
        int $levelSearch = SearchFieldHelper::EMPTY_FIELD,
        int $actual = SearchFieldHelper::EMPTY_FIELD
    ) {
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->programName = $programName;
        $this->authorSearch = $authorSearch;
        $this->branchSearch = $branchSearch;
        $this->focusSearch = $focusSearch;
        $this->allowSearch = $allowSearch;
        $this->levelSearch = $levelSearch;
        $this->actual = $actual;
        $this->actualRelevance = $activeValidators;
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
            $params['SearchTrainingProgram']['branchSearch'] = StringFormatter::stringAsInt($params['SearchTrainingProgram']['branchSearch']);
            $params['SearchTrainingProgram']['focusSearch'] = StringFormatter::stringAsInt($params['SearchTrainingProgram']['focusSearch']);
            $params['SearchTrainingProgram']['allowSearch'] = StringFormatter::stringAsInt($params['SearchTrainingProgram']['allowSearch']);
            $params['SearchTrainingProgram']['levelSearch'] = StringFormatter::stringAsInt($params['SearchTrainingProgram']['levelSearch']);
            $params['SearchTrainingProgram']['actual'] = StringFormatter::stringAsInt($params['SearchTrainingProgram']['actual']);
            $params['SearchTrainingProgram']['actualRelevance'] = StringFormatter::stringAsInt($params['SearchTrainingProgram']['actualRelevance']);
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

        $query = TrainingProgramWork::find()
            ->joinWith([
                'authorsProgramWork' => function ($query) {
                    $query->alias('author');
                },
                'authorsProgramWork.author' => function ($query) {
                    $query->alias('authorPeople');
                },
                'branchProgramWork' => function ($query) {
                    $query->alias('branch');
                },
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['ped_council_date' => SORT_DESC, 'name' => SORT_ASC]]
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
        $dataProvider->sort->attributes['namePretty'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['levelNumber'] = [
            'asc' => ['level' => SORT_ASC],
            'desc' => ['level' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['branchString'] = [
            'asc' => ['branch.branch' => SORT_ASC],
            'desc' => ['branch.branch' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['pedCouncilDate'] = [
            'asc' => ['ped_council_date' => SORT_DESC],
            'desc' => ['ped_council_date' => SORT_ASC],
        ];

        $dataProvider->sort->attributes['authorString'] = [
            'asc' => ['authorPeople.surname' => SORT_ASC],
            'desc' => ['authorPeople.surname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['capacity'] = [
            'asc' => ['capacity' => SORT_ASC],
            'desc' => ['capacity' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['agePeriod'] = [
            'asc' => ['student_left_age' => SORT_ASC, 'student_right_age' => SORT_ASC],
            'desc' => ['student_right_age' => SORT_DESC, 'student_left_age' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['focusString'] = [
            'asc' => ['focus' => SORT_ASC],
            'desc' => ['focus' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['allowRemote'] = [
            'asc' => ['allow_remote' => SORT_ASC],
            'desc' => ['allow_remote' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['fullDirectionName'] = [
            'asc' => ['thematic_direction' => SORT_ASC],
            'desc' => ['thematic_direction' => SORT_DESC],
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
        $this->filterName($query);
        $this->filterAuthor($query);
        $this->filterLevel($query);
        $this->filterFocus($query);
        $this->filterAllowRemote($query);
        $this->filterActual($query);
        $this->filterActualRelevance($query);
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

            $query->andWhere(['between', 'ped_council_date', $dateFrom, $dateTo]);
        }
    }

    /**
     * Поиск по отделам (месту реализации)
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterBranch(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->branchSearch) && $this->branchSearch !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['branch.branch' => $this->branchSearch]);
        }
    }

    /**
     * Поиск по названию
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterName(ActiveQuery $query) {
        if (!empty($this->programName)) {
            $query->andFilterWhere(['like', 'LOWER(name)', $this->programName]);
        }
    }

    /**
     * Поиск по составителям
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterAuthor(ActiveQuery $query) {
        if (!empty($this->authorSearch)) {
            $query->andFilterWhere(['like', 'LOWER(authorPeople.surname)', mb_strtolower($this->authorSearch)]);
        }
    }

    /**
     * Поиск по уровню сложности
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterLevel(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->levelSearch) && $this->levelSearch !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['level' => $this->levelSearch]);
        }
    }

    /**
     * Поиск по направленности
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterFocus(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->focusSearch) && $this->focusSearch !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['focus' => $this->focusSearch]);
        }
    }

    /**
     * Поиск по форме реализации
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterAllowRemote(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->allowSearch) && $this->allowSearch !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['allow_remote' => $this->allowSearch]);
        }
    }

    /**
     * Фильтр по актуальности программ
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterActual(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->actual) && $this->actual !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['actual' => $this->actual]);
        }
    }

    /**
     * Фильтр по актуальности программ для страницы массовой актуализации
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterActualRelevance(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->actualRelevance) && $this->actualRelevance !== SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['actual' => $this->actualRelevance]);
        }
    }
}
