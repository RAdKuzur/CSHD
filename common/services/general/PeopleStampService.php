<?php

namespace common\services\general;

use common\repositories\dictionaries\PeopleRepository;
use common\repositories\general\PeopleStampRepository;
use DomainException;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;

class PeopleStampService
{
    private PeopleRepository $peopleRepository;
    private PeopleStampRepository $stampRepository;

    public function __construct(PeopleRepository $peopleRepository, PeopleStampRepository $stampRepository)
    {
        $this->peopleRepository = $peopleRepository;
        $this->stampRepository = $stampRepository;
    }

    /**
     * Создает копию человека по его id
     * Возвращает id копии
     * @param $peopleId
     * @return int
     */
    public function createStampFromPeople($peopleId)
    {
        /** @var PeopleWork $people */
        $people = $this->peopleRepository->get($peopleId);
        if ($people) {
            $stamp = $this->stampRepository->getSimilar($people);
            /** @var PeoplePositionCompanyBranchWork $positionsCompanies */
            $positionsCompanies = $this->peopleRepository->getLastPositionsCompanies($peopleId);

            if ($stamp == null) {
                if($positionsCompanies) {
                    $stamp = PeopleStampWork::fill($people->id, $people->surname, $people->genitive_surname, $positionsCompanies->position_id, $positionsCompanies->company_id);
                }
                else {
                    $stamp = PeopleStampWork::fill($people->id, $people->surname, $people->genitive_surname, null,null);
                }
            }

            return $this->stampRepository->save($stamp);
        }
        else {
            throw new DomainException('Невозможно создать копию человека');
        }
    }

}