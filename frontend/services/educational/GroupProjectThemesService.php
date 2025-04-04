<?php

namespace frontend\services\educational;

use common\components\traits\CommonDatabaseFunctions;
use common\models\scaffold\GroupProjectThemes;
use common\models\scaffold\TrainingGroupParticipant;
use common\repositories\educational\GroupProjectThemesRepository;
use common\services\DatabaseServiceInterface;

class GroupProjectThemesService implements DatabaseServiceInterface
{
    private GroupProjectThemesRepository $repository;

    public function __construct(
        GroupProjectThemesRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function isAvailableDelete($entityId)
    {
        $addToGroup = $this->repository->checkDeleteAvailable(TrainingGroupParticipant::tableName(), GroupProjectThemes::tableName(), $entityId);

        return $addToGroup;
    }

    public function delete($entityId)
    {
        if (count($this->isAvailableDelete($entityId)) > 0) {
            return false;
        }

        $model = $this->repository->get($entityId);
        return $this->repository->delete($model);
    }
}