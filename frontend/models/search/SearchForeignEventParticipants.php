<?php

namespace frontend\models\search;

use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchForeignEventParticipants represents the model behind the search form of `app\models\common\ForeignEventParticipants`.
 */
class SearchForeignEventParticipants extends ForeignEventParticipantsWork
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['firstname', 'surname', 'patronymic'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $sort)
    {
        $query = ForeignEventParticipantsWork::find();
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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['surname' => SORT_ASC, 'firstname' => SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        /*$query->andFilterWhere([
            'id' => $this->id,
            //'firstname' => $this->firstname,
            //'secondname' => $this->secondname,
            //'patronymic' => $this->patronymic,
        ])
        ->andFilterWhere(['like', 'firstname', $this->firstname])
        ->andFilterWhere(['like', 'secondname', $this->secondname])
        ->andFilterWhere(['like', 'patronymic', $this->patronymic]);*/

        return $dataProvider;
    }
}
