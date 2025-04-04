<?php

namespace backend\services;

use backend\forms\TokensForm;
use common\helpers\common\SqlHelper;
use common\repositories\rubac\PermissionTokenRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\rubac\PermissionTokenWork;
use Yii;
use yii\db\Exception;

class PermissionTokenService
{
    const EVENT_NAME_PREFIX = 'token_event_';

    private PermissionTokenRepository $repository;
    private UserPermissionFunctionRepository $userPermissionRepository;

    public function __construct(
        PermissionTokenRepository $repository,
        UserPermissionFunctionRepository $userPermissionRepository
    )
    {
        $this->repository = $repository;
        $this->userPermissionRepository = $userPermissionRepository;
    }

    public function saveToken(TokensForm $form) : bool
    {
        if (!$this->checkDuplicate($form)) {
            Yii::$app->session->setFlash('danger', 'У данного пользователя уже есть данное разрешение');
            return false;
        }

        $currentTime = date('Y-m-d H:i:s');
        $model = PermissionTokenWork::fill(
            $form->userId,
            $form->permissionId,
            $currentTime,
            date('Y-m-d H:i:s', strtotime($currentTime) + $form->duration * 3600),
            $form->branch
        );

        $this->repository->save($model);

        $this->addDeleteEventForToken($model);
        return true;
    }

    public function checkDuplicate(TokensForm $form)
    {
        $duplicate = $this->repository->findByUserFunctionBranch(
            $form->userId,
            $form->permissionId,
            $form->branch
        );

        $duplicateFromPermissions = $this->userPermissionRepository->getByUserPermissionBranch(
            $form->userId,
            $form->permissionId,
            $form->branch
        );

        return is_null($duplicate) && is_null($duplicateFromPermissions);
    }

    public function addDeleteEventForToken(PermissionTokenWork $model)
    {
        $deleteEvent = SqlHelper::createDeleteEvent(
            self::EVENT_NAME_PREFIX . $model->id,
            $model->end_time,
            'permission_token',
            "WHERE `id`= $model->id"
        );

        try {
            Yii::$app->db->createCommand($deleteEvent)->execute();
        } catch (Exception $e) {
            Yii::error("Ошибка выполнения команды: " . $e->getMessage());
        }
    }

    public function deleteToken(int $id)
    {
        /** @var PermissionTokenWork $token */
        $token = $this->repository->get($id);
        $result = $this->repository->delete($token);

        $dropEvent = SqlHelper::dropEvent(self::EVENT_NAME_PREFIX . $id);
        try {
            Yii::$app->db->createCommand($dropEvent)->execute();
        } catch (Exception $e) {
            Yii::error("Ошибка выполнения команды: " . $e->getMessage());
        }

        return $result;
    }
}