<?php

namespace frontend\models\work\general;

use common\models\scaffold\PeoplePositionCompanyBranch;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\dictionaries\PositionWork;

/**
 * @property CompanyWork $companyWork
 * @property PositionWork $positionWork
 */

class PeoplePositionCompanyBranchWork extends PeoplePositionCompanyBranch
{
    public static function fill(
        $people_id,
        $position_id,
        $company_id,
        $branch
    )
    {
        $entity = new static();
        $entity->people_id = $people_id;
        $entity->position_id = $position_id;
        $entity->company_id = $company_id;
        $entity->branch = $branch;
        return $entity;
    }

    public function getPositionName()
    {
        return $this->positionWork->getPositionName();
    }

    public function getCompanyWork()
    {
        return $this->hasOne(CompanyWork::class, ['id' => 'company_id']);
    }

    public function getPositionWork()
    {
        return $this->hasOne(PositionWork::class, ['id' => 'position_id']);
    }

    public function getCompanyPositionString()
    {
        return $this->companyWork->name . " (" . $this->positionWork->name . ")";
    }
}
