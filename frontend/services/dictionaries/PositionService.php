<?php

namespace frontend\services\dictionaries;

use common\models\scaffold\PeoplePositionCompanyBranch;
use common\models\scaffold\PeopleStamp;
use common\models\scaffold\Position;
use common\repositories\general\PeoplePositionCompanyBranchRepository;
use common\repositories\general\PeopleStampRepository;
use common\services\DatabaseServiceInterface;

class PositionService implements DatabaseServiceInterface
{
    private PeoplePositionCompanyBranchRepository $peoplePositionCompanyBranchRepository;
    private PeopleStampRepository $peopleStampRepository;

    public function __construct(
        PeoplePositionCompanyBranchRepository $peoplePositionCompanyBranchRepository,
        PeopleStampRepository $peopleStampRepository
    )
    {
        $this->peoplePositionCompanyBranchRepository = $peoplePositionCompanyBranchRepository;
        $this->peopleStampRepository = $peopleStampRepository;
    }

    /**
     * Возвращает список ошибок, если список пуст - проблем нет
     * @param $entityId
     * @return array
     */
    public function isAvailableDelete($entityId)
    {
        $people = $this->peoplePositionCompanyBranchRepository->checkDeleteAvailable(PeoplePositionCompanyBranch::tableName(), Position::tableName(), $entityId);
        $peopleStamp = $this->peopleStampRepository->checkDeleteAvailable(PeopleStamp::tableName(), Position::tableName(), $entityId);

        return array_merge($people, $peopleStamp);
    }
}