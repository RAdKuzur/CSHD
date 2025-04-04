<?php

namespace backend\events\user;

use common\events\EventInterface;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use Yii;

class AddUserPermissionEvent implements EventInterface
{
    private int $userId;
    private int $functionId;

    private UserPermissionFunctionRepository $repository;

    public function __construct(
        int $userId,
        int $functionId
    )
    {
        $this->userId = $userId;
        $this->functionId = $functionId;
        $this->repository = Yii::createObject(UserPermissionFunctionRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareCreate(
                $this->userId,
                $this->functionId
            )
        ];
    }
}