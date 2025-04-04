<?php

namespace common\repositories\educational;

use common\models\scaffold\TrainingGroupExpert;
use common\repositories\providers\group_expert\TrainingGroupExpertProvider;
use common\repositories\providers\group_expert\TrainingGroupExpertProviderInterface;
use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use Yii;

class TrainingGroupExpertRepository
{
    private $provider;

    public function __construct(
        TrainingGroupExpertProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(TrainingGroupExpertProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getExpertsFromGroup($groupId, $type = [TrainingGroupExpertWork::TYPE_EXTERNAL, TrainingGroupExpertWork::TYPE_INTERNAL])
    {
        return $this->provider->getExpertsFromGroup($groupId, $type);
    }

    public function prepareCreate($groupId, $expertId, $expertType)
    {
        if (get_class($this->provider) == TrainingGroupExpertProvider::class) {
            return $this->provider->prepareCreate($groupId, $expertId, $expertType);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareCreate');
        }
    }

    public function prepareDelete($id)
    {
        if (get_class($this->provider) == TrainingGroupExpertProvider::class) {
            return $this->provider->prepareDelete($id);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareDelete');
        }
    }

    public function prepareUpdate($id, $expertId, $expertType)
    {
        if (get_class($this->provider) == TrainingGroupExpertProvider::class) {
            return $this->provider->prepareUpdate($id, $expertId, $expertType);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareUpdate');
        }
    }

    public function save(TrainingGroupExpertWork $expert)
    {
        return $this->provider->save($expert);
    }
}