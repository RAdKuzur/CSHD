<?php

namespace common\repositories\dictionaries;

use common\models\scaffold\ForeignEventParticipants;
use common\repositories\providers\participant\ParticipantProvider;
use common\repositories\providers\participant\ParticipantProviderInterface;
use DomainException;
use frontend\events\foreign_event_participants\PersonalDataParticipantDetachEvent;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonalDataParticipantWork;
use frontend\models\work\general\RussianNamesWork;
use InvalidArgumentException;
use Yii;

class ForeignEventParticipantsRepository
{
    const SORT_ID = 0;
    const SORT_FIO = 1;

    private $provider;

    public function __construct(ParticipantProviderInterface $provider = null)
    {
        if (!$provider) {
            $provider = Yii::createObject(ParticipantProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getParticipants(array $ids)
    {
        return $this->provider->getParticipants($ids);
    }

    public function getParticipantsForMerge()
    {
        if (get_class($this->provider) == ParticipantProvider::class) {
            return $this->provider->getParticipantsForMerge();
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getSortedList');
        }
    }

    public function getSortedList($sort = self::SORT_ID)
    {
        if (get_class($this->provider) == ParticipantProvider::class) {
            return $this->provider->getSortedList($sort);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getSortedList');
        }
    }

    public function prepareUpdate(ForeignEventParticipantsWork $model)
    {
        if (get_class($this->provider) == ParticipantProvider::class) {
            return $this->provider->prepareUpdate($model);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareUpdate');
        }
    }

    public function getSexByName(string $name)
    {
        if (get_class($this->provider) == ParticipantProvider::class) {
            return $this->provider->getSexByName($name);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getSexByName');
        }
    }
    public function getAll()
    {
        return ForeignEventParticipantsWork::find()->all();
    }
    public function delete(ForeignEventParticipantsWork $participant)
    {
        return $this->provider->delete($participant);
    }

    public function save(ForeignEventParticipantsWork $participant)
    {
        return $this->provider->save($participant);
    }
}