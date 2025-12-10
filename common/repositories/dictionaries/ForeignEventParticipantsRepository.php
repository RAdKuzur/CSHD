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
        return ForeignEventParticipantsWork::find()->orderBy(['surname' => SORT_ASC, 'firstname' => SORT_ASC, 'patronymic' => SORT_ASC])->all();
    }
    public function delete(ForeignEventParticipantsWork $participant)
    {
        return $this->provider->delete($participant);
    }

    public function getParticipantByUniqueData(string $firstname, string $surname, string $patronymic, string $birthdate)
    {
        return ForeignEventParticipantsWork::find()
            ->where(['firstname' => $firstname])
            ->andWhere(['surname' => $surname])
            ->andWhere(['patronymic' => $patronymic])
            ->andWhere(['birthdate' => $birthdate])
            ->one();
    }

    public function getParticipantByUniqueDataSingle($participant)
    {
        return ForeignEventParticipantsWork::find()
            ->where(['firstname' => $participant->firstname])
            ->andWhere(['surname' => $participant->surname])
            ->andWhere(['patronymic' => $participant->patronymic])
            ->andWhere(['birthdate' => $participant->birthdate])
            ->one();
    }


    public function participantExists(ForeignEventParticipantsWork $participant): bool
    {
        $query = ForeignEventParticipantsWork::find()
            ->where(['firstname' => $participant->firstname])
            ->andWhere(['surname' => $participant->surname])
            ->andWhere(['birthdate' => $participant->birthdate]);

        // Проверка отчества (NULL и пустая строка считаются одинаковыми)
        if (empty($participant->patronymic)) {
            $query->andWhere(['or', ['patronymic' => ''], ['patronymic' => null]]);
        } else {
            $query->andWhere(['patronymic' => $participant->patronymic]);
        }

        return $query->exists();
    }
    public function participantPossibleEmailChange(ForeignEventParticipantsWork $participant): bool
    {
        $query = ForeignEventParticipantsWork::find()
            ->where([
                'firstname' => $participant->firstname,
                'surname' => $participant->surname,
                'patronymic' => $participant->patronymic,
                'birthdate' => $participant->birthdate
            ]);

        if (!empty($participant->email)) {
            $query->andWhere(['or',
                ['!=', 'email', $participant->email],
                ['or', ['email' => ''], ['email' => null]]
            ]);
        } else {
            $query->andWhere(['is not', 'email', null])
                ->andWhere(['!=', 'email', '']);
        }

        return $query->exists();
    }
    //--------------------------------Добавлено---------------------------------
     public function findByFullName($firstname, $surname, $patronymic = null)
    {
        $query = ForeignEventParticipantsWork::find()
            ->where([
                'firstname' => trim($firstname),
                'surname' => trim($surname)
            ]);
        
        if (empty($patronymic)) {
            // Если отчество пустое, ищем записи с пустым или NULL отчеством
            $query->andWhere(['or', 
                ['patronymic' => ''], 
                ['patronymic' => null]
            ]);
        } else {
            $query->andWhere(['patronymic' => trim($patronymic)]);
        }
        
        return $query->one();
    }
    public function save(ForeignEventParticipantsWork $participant)
    {
        return $this->provider->save($participant);
    }
}