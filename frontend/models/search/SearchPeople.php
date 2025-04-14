<?php

namespace frontend\models\search;

use frontend\models\work\general\PeopleWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * SearchDocumentIn represents the model behind the search form of `app\models\common\DocumentIn`.
 */
class SearchPeople extends PeopleWork
{
    public string $name;
    public string $surname;
    public string $patronymic;
    public string $organized;
    public string $position;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'surname', 'patronymic', 'position', 'organized'], 'string'],
            [['name', 'surname', 'patronymic', 'position', 'organized'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function __construct(
        string $name = '',
        string $surname = '',
        string $patronymic = '',
        string $position = '',
        string $organized = '',
        $config = []
    )
    {
        parent::__construct($config);
        $this->name = $name;
        $this->surname = $surname;
        $this->patronymic = $patronymic;
        $this->position = $position;
        $this->organized = $organized;
    }

    /**
     * @param $params
     * @return void
     */
    public function loadParams($params)
    {
        $this->load($params);
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->loadParams($params);

        $query = PeopleWork::find()
            ->joinWith([
                'peoplePositionCompanyBranchWork' => function ($query) {
                    $query->alias('peoplePositionCompanyBranch');
                },
                'peoplePositionCompanyBranchWork.companyWork' => function ($query) {
                    $query->alias('companyWork');
                },
                'peoplePositionCompanyBranchWork.positionWork' => function ($query) {
                    $query->alias('positionWork');
                },
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['surname' => SORT_ASC, 'firstname' => SORT_ASC, 'patronymic' => SORT_ASC]]
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    /**
     * @param ActiveDataProvider $dataProvider
     * @return void
     */
    public function sortAttributes(ActiveDataProvider $dataProvider)
    {
        $dataProvider->sort->attributes['surname'] = [
            'asc' => ['surname' => SORT_ASC],
            'desc' => ['surname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['firstname'] = [
            'asc' => ['firstname' => SORT_ASC],
            'desc' => ['firstname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['patronymic'] = [
            'asc' => ['patronymic' => SORT_ASC],
            'desc' => ['patronymic' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['position'] = [
            'asc' => ['positionWork.name' => SORT_ASC],
            'desc' => ['positionWork.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['organized'] = [
            'asc' => ['companyWork.name' => SORT_ASC],
            'desc' => ['companyWork.name' => SORT_DESC],
        ];
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query)
    {
        $this->filterName($query);
        $this->filterSurname($query);
        $this->filterPatronymic($query);
        $this->filterPosition($query);
        $this->filterOrganized($query);
    }


    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterName(ActiveQuery $query) {
        if (!empty($this->name)) {
            $query->andWhere(['like', 'LOWER(firstname)', mb_strtolower($this->name)]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterSurname(ActiveQuery $query) {
        if (!empty($this->surname)) {
            $query->andWhere(['like', 'LOWER(surname)', mb_strtolower($this->surname)]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterPatronymic(ActiveQuery $query) {
        if (!empty($this->patronymic)) {
            $query->andWhere(['like', 'LOWER(patronymic)', mb_strtolower($this->patronymic)]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterPosition(ActiveQuery $query) {
        if (!empty($this->position)) {
            $query->andWhere(['like', 'LOWER(positionWork.name)', mb_strtolower($this->position)]);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterOrganized(ActiveQuery $query) {
        if (!empty($this->organized)) {
            $query->andWhere(['or',
                ['like', 'LOWER(companyWork.name)', mb_strtolower($this->organized)],
                ['like', 'LOWER(companyWork.short_name)', mb_strtolower($this->organized)]
            ]);
        }
    }
}
