<?php

namespace common\repositories\general;

use common\components\dictionaries\base\BranchDictionary;
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

    public function getResponsiblePeopleByBranch($branch)
    {
        // Определяем массив position_id в зависимости от ветки
        switch ($branch) {
            case BranchDictionary::TECHNOPARK:
                $positionIds = [25, 326, 16, 14];
                break;
            case BranchDictionary::CDNTT:
                $positionIds = [25, 20, 15, 14, 106, 44];
                break;
            case BranchDictionary::QUANTORIUM:
                $positionIds = [25, 18, 21, 15, 105];
                break;
            case BranchDictionary::COD:
                $positionIds = [25, 143, 15, 14, 34];
                break;
            default: // Мобильный Кванториум
                $positionIds = [25, 15];
        }

        // Выполняем запрос с фильтром WHERE IN
//        return ArrayHelper::getColumn(
//            PeoplePositionCompanyBranchWork::find()
//                ->where(['position_id' => $positionIds])
//                ->all(),
//            'people_id'
//        );
        // Выполняем запрос с фильтром WHERE IN и условием по branch
        return ArrayHelper::getColumn(
            PeoplePositionCompanyBranchWork::find()
                ->where(['position_id' => $positionIds])
                ->andWhere(['branch' => $branch]) // Добавляем фильтр по branch
                ->all(),
            'people_id'
        );
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