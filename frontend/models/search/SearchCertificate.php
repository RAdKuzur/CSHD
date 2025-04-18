<?php

namespace frontend\models\search;

use common\helpers\DateFormatter;
use common\helpers\search\SearchFieldHelper;
use common\helpers\StringFormatter;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\educational\CertificateWork;
use frontend\tests\UnitTester;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * SearchAuditorium represents the model behind the search form of `app\models\common\Auditorium`.
 */
class SearchCertificate extends CertificateWork
{
//    public $branchName;
    public string $certificateNumStr;
    public string $certificateTemplateStr;
    public string $participantStr;
    public string $trainingGroupStr;
    public string $startProtectionDate;
    public string $endProtectionDate;

    public string $protectionDate;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['certificateNumStr', 'certificateTemplateStr','participantStr','trainingGroupStr', 'startProtectionDate', 'endProtectionDate'], 'string'],
        ];
    }

    public function __construct(
        string $certificateNumStr = '',
        string $certificateTemplateStr = '',
        string $participantStr = '',
        string $trainingGroupStr = '',
        string $startProtectionDate = '',
        string $endProtectionDate = ''
        )
    {
        $this->certificateNumStr = $certificateNumStr;
        $this->certificateTemplateStr = $certificateTemplateStr;
        $this->participantStr = $participantStr;
        $this->trainingGroupStr = $trainingGroupStr;
        $this->startProtectionDate = $startProtectionDate;
        $this->endProtectionDate = $endProtectionDate;
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
     * @param $params
     * @return ActiveDataProvider
     */
    public function loadParams($params)
    {
        $this->load($params);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->loadParams($params);
        $query = CertificateWork::find()->joinWith(
            [
                'certificateTemplate certificateTemplate',
                'trainingGroupParticipant trainingGroupParticipant',
                'trainingGroupParticipant.participant participant',
                'trainingGroupParticipant.trainingGroup trainingGroup',
            ],
        );

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    public function sortAttributes(ActiveDataProvider $dataProvider)
    {
        $dataProvider->sort->attributes['participantStr'] = [
            'asc' => ['participant.surname' => SORT_ASC],
            'desc' => ['participant.surname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['trainingGroupStr'] = [
            'asc' => ['trainingGroup.number' => SORT_ASC],
            'desc' => ['trainingGroup.number' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['protectionDate'] = [
            'asc' => ['trainingGroup.protection_date' => SORT_ASC],
            'desc' => ['trainingGroup.protection_date' => SORT_DESC],
        ];
    }

    public function filterQueryParams(ActiveQuery $query)
    {
        $this->filterDate($query);
        $this->filterCertificateNum($query);
        $this->filterCertificateTemplate($query);
        $this->filterParticipant($query);
        $this->filterTrainingGroup($query);
    }

    private function filterDate(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->startProtectionDate) && $this->startProtectionDate != SearchFieldHelper::EMPTY_FIELD ||
            !StringFormatter::isEmpty($this->endProtectionDate) && $this->endProtectionDate != SearchFieldHelper::EMPTY_FIELD
        ) {
            $dateFrom = $this->startProtectionDate ? date('Y-m-d', strtotime($this->startProtectionDate)) : DateFormatter::DEFAULT_STUDY_YEAR_START;
            $dateTo = $this->endProtectionDate ? date('Y-m-d', strtotime($this->endProtectionDate)) : date('Y-m-d');

           $query->andWhere(
               ['between', 'trainingGroup.protection_date', $dateFrom, $dateTo]
           );
        }

    }

    private function filterCertificateNum(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->certificateNumStr) && $this->certificateNumStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['certificate_number' => $this->certificateNumStr]);
        }
    }

    private function filterCertificateTemplate(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->certificateTemplateStr) && $this->certificateTemplateStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(['like','certificateTemplate.name', $this->certificateTemplateStr]);
        }
    }

    private function filterParticipant(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->participantStr) && $this->participantStr != SearchFieldHelper::EMPTY_FIELD) {
             $query->andFilterWhere(['or',
                 ['like', 'participant.surname', $this->participantStr],
                 ['like', 'participant.firstname', $this->participantStr],
                 ['like', 'participant.patronymic', $this->participantStr],
             ]);
        }
    }

    private function filterTrainingGroup(ActiveQuery $query)
    {
        if (!StringFormatter::isEmpty($this->trainingGroupStr) && $this->trainingGroupStr != SearchFieldHelper::EMPTY_FIELD) {
            $query->andFilterWhere(
              ['like', 'trainingGroup.number', $this->trainingGroupStr],
            );
        }
    }

}
