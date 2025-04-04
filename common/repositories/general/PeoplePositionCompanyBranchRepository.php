<?php

namespace common\repositories\general;

use common\components\traits\CommonDatabaseFunctions;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use Yii;
use yii\helpers\ArrayHelper;

class PeoplePositionCompanyBranchRepository
{
    use CommonDatabaseFunctions;

    public function get($id)
    {
        return PeoplePositionCompanyBranchWork::find()->where(['id' => $id])->one();
    }

    public function getByPeople($peopleId)
    {
        return PeoplePositionCompanyBranchWork::find()->where(['people_id' => $peopleId])->orderBy(['id' => SORT_DESC])->all();
    }

    public function getPeopleByCompany($companyId)
    {
        return ArrayHelper::getColumn(
            PeoplePositionCompanyBranchWork::find()->where(['company_id' => $companyId])->all(),
            'people_id'
        );
    }

    public function getPeopleByPosition($positionId)
    {
        return ArrayHelper::getColumn(
            PeoplePositionCompanyBranchWork::find()->where(['position_id' => $positionId])->all(),
            'people_id'
        );
    }

    public function getPositionsByPeople($peopleId)
    {
        $peoplePositions = PeoplePositionCompanyBranchWork::find()->where(['people_id' => $peopleId])->all();
        return PositionWork::find()->where(['IN', 'id', ArrayHelper::getColumn($peoplePositions, 'position_id')])->all();
    }

    public function getCompaniesByPeople($peopleId)
    {
        $peoplePositions = PeoplePositionCompanyBranchWork::find()->where(['people_id' => $peopleId])->all();
        return PositionWork::find()->where(['IN', 'id', ArrayHelper::getColumn($peoplePositions, 'company_id')])->all();
    }

    public function delete(PeoplePositionCompanyBranchWork $model)
    {
        return $model->delete();
    }

    public function prepareCreate($people_id, $position_id, $company_id, $branch)
    {
        $model = PeoplePositionCompanyBranchWork::fill($people_id, $position_id, $company_id, $branch);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($modelId)
    {
        $model = $this->get($modelId);
        $command = Yii::$app->db->createCommand();
        $command->delete($model::tableName(), ['id' => $modelId]);
        return $command->getRawSql();
    }
}