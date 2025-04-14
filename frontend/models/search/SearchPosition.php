<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use frontend\models\work\dictionaries\PositionWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * SearchPosition represents the model behind the search form of `app\models\common\Position`.
 */
class SearchPosition extends Model implements SearchInterfaces
{
    public string $name;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function __construct(
        string $name = '',
        $config = []
    )
    {
        parent::__construct($config);
        $this->name = $name;
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

        $query = PositionWork::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]]
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
        $dataProvider->sort->attributes['name'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query)
    {
        $this->filterName($query);
    }


    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function filterName(ActiveQuery $query) {
        if (!empty($this->name)) {
            $query->andWhere(['like', 'LOWER(name)', mb_strtolower($this->name)]);
        }
    }
}
