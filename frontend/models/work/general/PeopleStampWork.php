<?php

namespace frontend\models\work\general;

use common\models\scaffold\PeopleStamp;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\dictionaries\PositionWork;
use InvalidArgumentException;

/**
 * @property PeopleWork $peopleWork
 * @property PositionWork $positionWork
 * @property CompanyWork $companyWork
 */
class PeopleStampWork extends PeopleStamp
{
    public static function fill($peopleId, $surname, $genitiveSurname, $positionId, $companyId)
    {
        $entity = new static();
        $entity->people_id = $peopleId;
        $entity->surname = $surname;
        $entity->genitive_surname = $genitiveSurname;
        $entity->position_id = $positionId;
        $entity->company_id = $companyId;

        return $entity;
    }

    public function getFIO($type)
    {
        switch ($type) {
            case PersonInterface::FIO_FULL:
                return $this->getFullFio();
            case PersonInterface::FIO_SURNAME_INITIALS:
                return $this->getSurnameInitials();
            case PersonInterface::FIO_WITH_POSITION:
                return $this->getFioPosition();
            case PersonInterface::FIO_SURNAME_INITIALS_WITH_POSITION:
                return $this->getPositionSurnameInitials();
            default:
                throw new InvalidArgumentException('Неизвестный тип вывода ФИО');
        }
    }

    public function getFullFio()
    {
        return "$this->surname {$this->peopleWork->firstname} {$this->peopleWork->patronymic}";
    }

    public function getSurnameInitials()
    {
        return $this->surname
            . ' ' . mb_substr($this->peopleWork->firstname, 0, 1)
            . '. ' . ($this->peopleWork->patronymic ? mb_substr($this->peopleWork->patronymic, 0, 1) . '.' : '');
    }

    public function getFioPosition()
    {
        return 'stub';
    }

    public function getPositionSurnameInitials()
    {
        return "{$this->getPositionName()} {$this->getSurnameInitials()}";
    }

    public function getPositionName()
    {
        return $this->positionWork ? $this->positionWork->getPositionName() : '';
    }

    public function getPeopleWork()
    {
        return $this->hasOne(PeopleWork::class, ['id' => 'people_id']);
    }

    public function getPositionWork()
    {
        return $this->hasOne(PositionWork::class, ['id' => 'position_id']);
    }

    public function getCompanyWork()
    {
        return $this->hasOne(CompanyWork::class, ['id' => 'company_id']);
    }
}
