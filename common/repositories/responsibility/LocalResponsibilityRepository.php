<?php

namespace common\repositories\responsibility;

use common\components\BaseConsts;
use DomainException;
use frontend\models\work\responsibility\LegacyResponsibleWork;
use frontend\models\work\responsibility\LocalResponsibilityWork;

class LocalResponsibilityRepository
{
    private LegacyResponsibleRepository $legacyRepository;

    public function __construct(LegacyResponsibleRepository $legacyRepository)
    {
        $this->legacyRepository = $legacyRepository;
    }

    public function get($id)
    {
        return LocalResponsibilityWork::find()->where(['id' => $id])->one();
    }

    public function getPeopleResponsibilities($peopleId)
    {
        return LocalResponsibilityWork::find()
            ->joinWith(['peopleStampWork peopleStampWork'])
            ->where(['peopleStampWork.people_id' => $peopleId])
            ->all();
    }

    public function save(LocalResponsibilityWork $responsibility)
    {
        if (!$responsibility->save()) {
            throw new DomainException('Ошибка сохранения ответственности. Проблемы: '.json_encode($responsibility->getErrors()));
        }

        return $responsibility->id;
    }

    public function delete(LocalResponsibilityWork $responsibility)
    {
        $legacies = $this->legacyRepository->getByResponsibility($responsibility, BaseConsts::QUERY_ALL);
        foreach ($legacies as $legacy) {
            /** @var LegacyResponsibleWork $legacy */
            $this->legacyRepository->delete($legacy);
        }

        if (!$responsibility->delete()) {
            throw new DomainException('Ошибка удаления ответственности. Проблемы: '.json_encode($responsibility->getErrors()));
        }
    }
}