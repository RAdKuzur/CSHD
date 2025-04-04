<?php

namespace frontend\events\dictionaries;

use common\events\EventInterface;
use common\models\scaffold\PeoplePositionCompanyBranch;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\general\PeoplePositionCompanyBranchRepository;
use Yii;

class PeoplePositionCompanyBranchEventCreate implements EventInterface
{
    private $people_id;
    private $position_id;
    private $company_id;
    private $branch;
    private PeoplePositionCompanyBranchRepository $repository;

    public function __construct(
        $people_id, $position_id, $company_id, $branch
    )
    {
        $this->people_id = $people_id;
        $this->position_id = $position_id;
        $this->company_id = $company_id;
        $this->branch = $branch;
        $this->repository = Yii::createObject(PeoplePositionCompanyBranchRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        // TODO: Implement execute() method.
        return [
            $this->repository->prepareCreate(
                $this->people_id ,
                $this->position_id ,
                $this->company_id  ,
                $this->branch
            )
        ];
    }
}
