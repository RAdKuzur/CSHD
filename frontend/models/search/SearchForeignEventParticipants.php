<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\web\GroupUrlRule;


class SearchForeignEventParticipants extends Model implements SearchInterfaces
{
    public string $participantName;
    public int $branch;
    public int $restrictions;   // ограничения ПД
    public int $incorrect;      // некорректные данные

    public const RESTRICTIONS = [0 => '---', 1 => 'С ограничениями ПД', 2 => 'Без ограничения ПД'];
    public const INCORRECT = [0 => '---', 1 => 'Некорректные данные', 2 => 'Корректные данные'];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'branch', 'restrictions', 'incorrect'], 'integer'],
            [['firstname', 'surname', 'patronymic', 'participantName'], 'string'],
            [['participantName', 'branch', 'restrictions', 'incorrect'], 'safe'],
        ];
    }

    public function __construct(
        string $participantName = '',
        int $branch = SearchFieldHelper::EMPTY_FIELD,
        int $restrictions = SearchFieldHelper::EMPTY_FIELD,
        int $incorrect = SearchFieldHelper::EMPTY_FIELD
    ) {
        $this->participantName = $participantName;
        $this->branch = $branch;
        $this->restrictions = $restrictions;
        $this->incorrect = $incorrect;
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
            $params['SearchForeignEventParticipants']['branch'] = StringFormatter::stringAsInt($params['SearchForeignEventParticipants']['branch']);
            $params['SearchForeignEventParticipants']['restrictions'] = StringFormatter::stringAsInt($params['SearchForeignEventParticipants']['restrictions']);
            $params['SearchForeignEventParticipants']['incorrect'] = StringFormatter::stringAsInt($params['SearchForeignEventParticipants']['incorrect']);
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

        $query = ForeignEventParticipantsWork::find()
            /*->joinWith([
                'personalDataParticipantWork' => function ($query) {
                    $query->alias('personalData');
                }
            ])*/;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['surname' => SORT_ASC, 'firstname' => SORT_ASC]]
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
        $dataProvider->sort->attributes['firstname'] = [
            'asc' => ['firstname' => SORT_ASC],
            'desc' => ['firstname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['secondname'] = [
            'asc' => ['secondname' => SORT_ASC],
            'desc' => ['secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['patronymic'] = [
            'asc' => ['patronymic' => SORT_ASC],
            'desc' => ['patronymic' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sex'] = [
            'asc' => ['sex' => SORT_DESC],
            'desc' => ['sex' => SORT_ASC],
        ];

        $dataProvider->sort->attributes['birthdate'] = [
            'asc' => ['birthdate' => SORT_DESC],
            'desc' => ['birthdate' => SORT_ASC],
        ];
    }


    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query) {
        $this->filterParticipant($query);
        $this->filterIncorrect($query);
        $this->filterRestrictions($query);
    }

    /**
     * Поиск по фамилии или имени участника деятельности
     * @param ActiveQuery $query
     * @return void
     */
    public function filterParticipant(ActiveQuery $query) {
        if (!empty($this->participantName)) {
            $query->andFilterWhere(['like', 'LOWER(surname)', mb_strtolower($this->participantName)])
            ->orWhere(['like', 'LOWER(firstname)', mb_strtolower($this->participantName)]);
        }
    }

    /**
     * Фильтрация по корректности заполнения карточки
     * @param ActiveQuery $query
     * @return void
     */
    public function filterIncorrect(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->incorrect) && $this->incorrect !== SearchFieldHelper::EMPTY_FIELD) {
            switch ($this->incorrect) {
                case 1:
                    $query->andFilterWhere(['OR',
                        ['sex' => 2],       // пол "другое"
                        ['!=', 'is_true', 1],        // система посчитала его не правильным
                        ['!=', 'guaranteed_true', 1],   // человеки не сказали что он точно правильный
                    ]);
                    break;
                case 2:
                    $query->andFilterWhere(['OR',
                        ['!=', 'sex', 2],       // пол не "другое"
                        ['is_true' => 1],        // система посчитала его правильным
                        ['guaranteed_true' => 1],   // человеки сказали что он точно правильный
                    ]);
                    break;
            }
        }
    }

    /**
     * Фильтрация по разрешению в персональных данных
     * @param ActiveQuery $query
     * @return void
     */
    public function filterRestrictions(ActiveQuery $query) {
        if (!StringFormatter::isEmpty($this->restrictions) && $this->restrictions !== SearchFieldHelper::EMPTY_FIELD) {
            $subQuery = (new Query())
                ->select('participant_id')
                ->distinct()
                ->from('personal_data_participant')
                ->where(['status' => 1]);

            switch ($this->restrictions) {
                case 1:
                    $query->andFilterWhere(['IN', 'id', $subQuery]);
                    break;
                case 2:
                    $query->andFilterWhere(['NOT IN', 'id', $subQuery]);
                    break;
            }
        }
    }
}
