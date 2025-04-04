<?php

namespace frontend\services\dictionaries;

use common\models\scaffold\Company;
use common\models\scaffold\DocumentIn;
use common\models\scaffold\DocumentOut;
use common\models\scaffold\PeoplePositionCompanyBranch;
use common\models\scaffold\PeopleStamp;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\general\PeoplePositionCompanyBranchRepository;
use common\repositories\general\PeopleStampRepository;
use common\services\DatabaseServiceInterface;

class CompanyService implements DatabaseServiceInterface
{
    private DocumentInRepository $documentInRepository;
    private DocumentOutRepository $documentOutRepository;
    private PeoplePositionCompanyBranchRepository $peoplePositionCompanyBranchRepository;
    private PeopleStampRepository $peopleStampRepository;

    public function __construct(
        DocumentInRepository $documentInRepository,
        DocumentOutRepository $documentOutRepository,
        PeoplePositionCompanyBranchRepository $peoplePositionCompanyBranchRepository,
        PeopleStampRepository $peopleStampRepository
    )
    {
        $this->documentInRepository = $documentInRepository;
        $this->documentOutRepository = $documentOutRepository;
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
        $docsIn = $this->documentInRepository->checkDeleteAvailable(DocumentIn::tableName(), Company::tableName(), $entityId);
        $docsOut = $this->documentOutRepository->checkDeleteAvailable(DocumentOut::tableName(), Company::tableName(), $entityId);
        $people = $this->peoplePositionCompanyBranchRepository->checkDeleteAvailable(PeoplePositionCompanyBranch::tableName(), Company::tableName(), $entityId);
        $peopleStamp = $this->peopleStampRepository->checkDeleteAvailable(PeopleStamp::tableName(), Company::tableName(), $entityId);

        return array_merge($docsIn, $docsOut, $people, $peopleStamp);
    }
}