<?php

namespace frontend\events\dictionaries;

use common\events\EventInterface;
use common\models\scaffold\PeoplePositionCompanyBranch;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\general\PeoplePositionCompanyBranchRepository;
use Yii;

class PeoplePositionCompanyBranchEventDelete implements EventInterface
{
    private $modelId;
    private PeoplePositionCompanyBranchRepository $repository;

    public function __construct(
        $modelId
    )
    {
        $this->modelId = $modelId;
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
            $this->repository->prepareDelete(
                $this->modelId
            )
        ];
    }
}
