<?php

namespace common\repositories\dictionaries;

use frontend\events\dictionaries\PeoplePositionCompanyBranchEventDelete;
use common\components\traits\CommonDatabaseFunctions;
use common\helpers\SortHelper;
use common\repositories\general\PeoplePositionCompanyBranchRepository;
use common\repositories\providers\people\PeopleProvider;
use common\repositories\providers\people\PeopleProviderInterface;
use DomainException;
use frontend\models\work\general\PeopleWork;
use Yii;
use yii\db\ActiveQuery;

class PeopleRepository
{
    use CommonDatabaseFunctions;

    private $provider;

    public function __construct(
        PeopleProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(PeopleProvider::class);
        }

        $this->provider = $provider;
    }

    public function prepareCreate($name, $surname, $patronymic)
    {
        if (get_class($this->provider) == PeopleProvider::class) {
            return $this->provider->prepareCreate($name, $surname, $patronymic);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareCreate');
        }
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getAll()
    {
        return $this->provider->getAll();
    }

    public function getPositionsCompanies($id)
    {
        if (get_class($this->provider) == PeopleProvider::class) {
            return $this->provider->getPositionsCompanies($id);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getPositionsCompanies');
        }
    }

    public function getLastPositionsCompanies($id)
    {
        if (get_class($this->provider) == PeopleProvider::class) {
            return $this->provider->getLastPositionsCompanies($id);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getLastPositionsCompanies');
        }
    }

    public function getCompaniesPositionsByPeople($peopleId)
    {
        if (get_class($this->provider) == PeopleProvider::class) {
            return $this->provider->getCompaniesPositionsByPeople($peopleId);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getCompaniesPositionsByPeople');
        }
    }

    /**
     * Возвращает сортированный список людей
     * @param int $orderedType тип сортировки
     * @param int $orderDirection направление сортировки @see standard_defines
     * @param ActiveQuery $baseQuery базовый запрос, который необходимо упорядочить (при наличии)
     * @return array|\yii\db\ActiveQuery|\yii\db\ActiveRecord[]
     */
    public function getOrderedList(int $orderedType = SortHelper::ORDER_TYPE_ID, int $orderDirection = SORT_DESC, $baseQuery = null)
    {
        if (get_class($this->provider) == PeopleProvider::class) {
            return $this->provider->getOrderedList($orderedType, $orderDirection, $baseQuery);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getOrderedList');
        }
    }

    public function getPeopleFromMainCompany()
    {
        if (get_class($this->provider) == PeopleProvider::class) {
            return $this->provider->getPeopleFromMainCompany();
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getPeopleFromMainCompany');
        }
    }

    public function deletePosition($id)
    {
        if (get_class($this->provider) == PeopleProvider::class) {
            return $this->provider->deletePosition($id);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода deletePosition');
        }
    }

    public function getByIds($ids)
    {
        return PeopleWork::find()->where(['id' => $ids])->all();
    }

    public function save(PeopleWork $people)
    {
        return $this->provider->save($people);
    }

    public function delete(PeopleWork $model)
    {
        return $this->provider->delete($model);
    }
}