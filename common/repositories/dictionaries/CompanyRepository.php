<?php

namespace common\repositories\dictionaries;

use DomainException;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use yii\helpers\ArrayHelper;

class CompanyRepository
{
    public function get($id)
    {
        return CompanyWork::find()->where(['id' => $id])->one();
    }

    /**
     * Возвращает список организаций
     * @param int|null $peopleId если передан параметр, то возвращает текущую организацию человека @see PeopleWork
     * @return array
     */
    public function getList(int $peopleId = null): array
    {
        $query = CompanyWork::find();
        if ($peopleId) {
            $subQuery = PeoplePositionCompanyBranchWork::find()->where(['people_id' => $peopleId])->all();
            $query->andWhere(['IN', 'id', ArrayHelper::getColumn($subQuery, 'company_id')]);
        }

        return $query->all();
    }

    public function fastCreateWithId(
        $id,
        $name,
        $shortName,
        $isContractor
    )
    {
        return CompanyWork::fastFillWithId($id, $name, $shortName, $isContractor);
    }

    public function delete(CompanyWork $company)
    {
        if (!$company->delete()) {
            throw new DomainException('Ошибка удаления организации. Проблемы: '.json_encode($company->getErrors()));
        }

        return $company->id;
    }

    public function save(CompanyWork $company)
    {
        if (!$company->save()) {
            throw new DomainException('Ошибка привязки правила к пользователю. Проблемы: '.json_encode($company->getErrors()));
        }

        return $company->id;
    }
}