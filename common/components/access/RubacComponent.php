<?php

namespace common\components\access;

use common\repositories\general\UserRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\rubac\PermissionFunctionWork;
use Yii;
use yii\helpers\ArrayHelper;

class RubacComponent
{
    private UserPermissionFunctionRepository $userPermissionFunctionRepository;
    private UserRepository $userRepository;
    private RulesConfig $racConfig;
    private AuthDataCache $authCache;
    private $permissions = [];

    public function __construct(
        UserPermissionFunctionRepository $userPermissionFunctionRepository,
        RulesConfig $racConfig,
        UserRepository $userRepository,
        AuthDataCache $authCache
    )
    {
        $this->userPermissionFunctionRepository = $userPermissionFunctionRepository;
        $this->racConfig = $racConfig;
        $this->userRepository = $userRepository;
        $this->authCache = $authCache;
    }

    public function init()
    {
        if (Yii::$app->user->identity->getId()) {
            $userId = Yii::$app->user->identity->getId();
            $permissions = $this->userPermissionFunctionRepository->getPermissionsByUser($userId);
            $this->permissions = $permissions;
            $this->authCache->loadDataFromPermissions($permissions, $userId);
            return true;
        }

        return false;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Проверка доступа к экшну для конкретного пользователя
     *
     * @param $userId
     * @param $controller
     * @param $action
     * @return bool
     */
    public function checkUserAccess($userId, $controller, $action) : bool
    {
        $this->authCache->loadDataFromDB($userId);
        $permissions = $this->authCache->getAllPermissionsFromUser($userId);
        if (!$permissions) {
            $permissions = ArrayHelper::getColumn($this->userPermissionFunctionRepository->getPermissionsByUser($userId), 'short_code');
        }

        foreach ($permissions as $permission) {
            if ($this->checkAllow($permission, $controller, $action->id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Определяет, разрешает ли правило $rule получить доступ к экшну $controller/$action
     *
     * @param $rule
     * @param $controller
     * @param $action
     * @return bool
     */
    public function checkAllow($rule, $controller, $action)
    {
        $permissions = $this->racConfig->getAllPermissions();
        return array_key_exists($rule, $permissions)
            && array_key_exists($controller, $permissions[$rule])
            && in_array($action, $permissions[$rule][$controller]);
    }

    public function isGuest() : bool
    {
        return Yii::$app->user->isGuest;
    }

    public function authId()
    {
        return Yii::$app->user->identity->getId();
    }

    public function checkPermission(int $userId, string $permissionCode)
    {
        $permissions = ArrayHelper::getColumn(
            $this->userPermissionFunctionRepository->getPermissionsByUser($userId),
            'short_code'
        );
        return in_array($permissionCode, $permissions);
    }

    public function getTeacherPermissions()
    {
        return [
            'add_group',
            'view_self_groups',
            'edit_self_groups',
            'view_dictionaries',
            'view_training_programs',
            'view_study_orders'
        ];
    }

    public function getStudyInformantPermissions()
    {
        return [
            'add_group',
            'view_branch_groups',
            'edit_branch_groups',
            'view_dictionaries',
            'view_training_programs',
            'edit_training_programs',
            'view_study_orders',
            'edit_study_orders',
            'create_certificates',
            'view_certificate_template',
            'edit_certificate_template'
        ];
    }

    public function getEventInformantPermissions()
    {
        return [
            'view_dictionaries',
            'view_base_orders',
            'view_event_regulations',
            'edit_event_regulations',
            'view_events',
            'edit_events',
            'view_foreign_events',
            'edit_foreign_events',
        ];
    }

    public function getDocumentInformantPermissions()
    {
        return [
            'view_doc_in',
            'edit_doc_in',
            'view_doc_out',
            'edit_doc_out',
            'view_base_orders',
            'edit_base_orders',
            'view_local_resp'
        ];
    }

    public function getBranchControllerPermissions()
    {
        return [
            'add_group',
            'view_branch_groups',
            'edit_branch_groups',
            'delete_branch_groups',
            'forgive_study_errors',
            'archive_branch_groups',
            'view_dictionaries',
            'edit_dictionaries',
            'view_training_programs',
            'edit_training_programs',
            'view_study_orders',
            'edit_study_orders',
            'gen_report_query',
            'view_doc_in',
            'view_doc_out',
            'view_base_orders',
            'view_event_regulations',
            'edit_event_regulations',
            'view_base_regulations',
            'view_events',
            'edit_events',
            'view_foreign_events',
            'edit_foreign_events',
            'view_local_resp',
            'create_certificates',
            'view_certificate_template'
        ];
    }

    public function getSuperControllerPermissions()
    {
        return [
            'view_all_groups',
            'edit_all_groups',
            'delete_all_groups',
            'archive_all_groups',
            'view_dictionaries',
            'edit_dictionaries',
            'view_training_programs',
            'edit_training_programs',
            'view_study_orders',
            'edit_study_orders',
            'gen_report_query',
            'gen_report_forms',
            'view_doc_in',
            'edit_doc_in',
            'view_doc_out',
            'edit_doc_out',
            'view_base_orders',
            'edit_base_orders',
            'view_event_regulations',
            'edit_event_regulations',
            'view_base_regulations',
            'edit_base_regulations',
            'view_events',
            'edit_events',
            'view_foreign_events',
            'edit_foreign_events',
            'view_local_resp',
            'edit_local_resp',
            'view_users',
            'edit_users',
            'create_certificates',
            'delete_certificates',
            'view_certificate_template',
            'edit_certificate_template',
            'merge_participants'
        ];
    }

    public function getAdminPermissions()
    {
        return [
            'add_group',
            'view_self_groups',
            'view_branch_groups',
            'view_all_groups',
            'edit_self_groups',
            'edit_branch_groups',
            'edit_all_groups',
            'delete_branch_groups',
            'delete_all_groups',
            'archive_branch_groups',
            'archive_all_groups',
            'forgive_study_errors',
            'forgive_base_errors',
            'delete_participants',
            'merge_participants',
            'view_training_programs',
            'edit_training_programs',
            'view_event_orders',
            'edit_event_orders',
            'view_study_orders',
            'edit_study_orders',
            'view_base_orders',
            'edit_base_orders',
            'gen_report_query',
            'gen_report_forms',
            'view_doc_in',
            'edit_doc_in',
            'view_doc_out',
            'edit_doc_out',
            'view_event_regulations',
            'edit_event_regulations',
            'view_base_regulations',
            'edit_base_regulations',
            'view_events',
            'edit_events',
            'view_foreign_events',
            'edit_foreign_events',
            'view_local_resp',
            'edit_local_resp',
            'view_users',
            'edit_users',
            'edit_permissions',
            'create_certificates',
            'delete_certificates',
            'allow_base_admin',
            'allow_extended_admin',
            'view_certificate_template',
            'edit_certificate_template',
            'view_material_obj',
            'edit_material_obj',
            'move_material_obj',
            'view_dictionaries',
            'edit_dictionaries'
        ];
    }
}