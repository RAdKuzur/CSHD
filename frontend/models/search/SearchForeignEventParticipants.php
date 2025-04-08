<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;


class SearchForeignEventParticipants extends Model implements SearchInterfaces
{
    public string $participantName;
    public int $branch;
    public int $restrictions;   // ограничения ПД
    public int $incorrect;      // некорректные данные

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
        /*if ($sort == 1)
        {
            //$str = "SELECT * FROM `foreign_event_participants` WHERE `is_true` <> 1 AND (`guaranted_true` IS NULL OR `guaranted_true` = 0)
            //       OR `sex` = 'Другое' AND (`guaranted_true` IS NULL OR `guaranted_true` = 0) ORDER BY `secondname`";
            $query = ForeignEventParticipantsWork::find()->where(['IN', 'id',
                (new Query())->select('id')->from('foreign_event_participants')->where(['!=', 'is_true', 1])->andWhere(['IN', 'id',
                    (new Query())->select('id')->from('foreign_event_participants')->where(['guaranted_true' => null])->orWhere(['guaranted_true' => 0])])])
                ->orWhere(['IN', 'id',
                    (new Query())->select('id')->from('foreign_event_participants')->where(['sex' => 'Другое'])->andWhere(['IN', 'id',
                        (new Query())->select('id')->from('foreign_event_participants')->where(['guaranted_true' => null])->orWhere(['guaranted_true' => 0])])]);
            //$query = ForeignEventParticipantsWork::findBySql($str);
        }
        if ($sort == 2)
        {
            $query = ForeignEventParticipantsWork::find()->where(['IN', 'id',
                (new Query())->select('foreign_event_participant_id')->distinct()->from('personal_data_foreign_event_participant')->where(['status' => 1])]);
        }

        // add conditions that should always apply here*/

        $this->loadParams($params);

        $query = ForeignEventParticipantsWork::find();
        /*$query = DocumentInWork::find()
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
            ]);*/

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
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    public function filterParticipant(ActiveQuery $query) {
        if (!empty($this->participantName)) {
            $query->andFilterWhere(['like', 'LOWER(surname)', mb_strtolower($this->participantName)])
            ->orWhere(['like', 'LOWER(firstname)', mb_strtolower($this->participantName)]);
        }
    }
}
