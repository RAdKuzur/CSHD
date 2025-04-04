<?php

namespace console\controllers;

use common\components\access\AuthDataCache;
use common\repositories\rubac\UserPermissionFunctionRepository;
use Exception;
use yii\console\Controller;

/**
 * Site controller
 */
class AccessModelController extends Controller
{
    private UserPermissionFunctionRepository $userFunctionRepository;
    private AuthDataCache $authCache;

    public function __construct(
                                         $id,
                                         $module,
        UserPermissionFunctionRepository $userFunctionRepository,
                           AuthDataCache $authCache,
                                         $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userFunctionRepository = $userFunctionRepository;
        $this->authCache = $authCache;
    }

    //-----------------------------------------------

    public function actionAttachTemplate()
    {
        $template = $this->prompt(
            'Введите название шаблона (teacher, study_info, event_info, doc_info, material_info, branch_controller, super_controller, admin):'
        );

        $userId = $this->prompt('Введите ID пользователя:');
        $branch = $this->prompt('Введите отдел для правила (1 - Технопарк, 2 - Кванториум, 3 - ЦДНТТ, 4 - ЦОД, 5 - Моб. Кванториум, 6 - Планетарий, 7 - Администрация) или оставьте пустым:');

        $hasException = false;
        try {
            $this->userFunctionRepository->attachTemplatePermissionsToUser($template, $userId, $branch);
        }
        catch (Exception $e) {
            $hasException = true;
            echo "Возникла ошибка во время назначения прав пользователю {$e->getMessage()}";
        }

        if (!$hasException) {
            echo "Права успешно назначены пользователю";
            $this->authCache->clearAuthData($userId);
        }
    }
}
