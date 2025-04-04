<?php

namespace backend\builders;

use common\components\dictionaries\base\BranchDictionary;
use frontend\models\work\dictionaries\AuditoriumWork;
use yii\db\ActiveQuery;

class AuditoriumReportBuilder
{
    public function query() : ActiveQuery
    {
        return AuditoriumWork::find();
    }

    public function filterByOwnership(ActiveQuery $query)
    {
        return $query->andWhere(['NOT IN', 'branch', [BranchDictionary::CDNTT, BranchDictionary::PLANETARIUM]]);
    }

    public function filterByRent(ActiveQuery $query)
    {
        return $query->andWhere(['IN', 'branch', [BranchDictionary::CDNTT, BranchDictionary::PLANETARIUM]]);
    }

    public function filterByType(ActiveQuery $query, array $types = [])
    {
        return $query->andWhere(['IN', 'auditorium_type', $types]);
    }

    public function filterByIncludeSquare(ActiveQuery $query, array $include = [AuditoriumWork::NO_INCLUDE, AuditoriumWork::IS_INCLUDE])
    {
        return $query->andWhere(['IN', 'include_square', $include]);
    }

    public function filterByEducation(ActiveQuery $query, array $education = [AuditoriumWork::NO_EDUCATION, AuditoriumWork::IS_EDUCATION])
    {
        return $query->andWhere(['IN', 'is_education', $education]);
    }
}