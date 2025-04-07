<?php

namespace console\controllers\copy;

use common\components\access\AuthDataCache;
use common\repositories\rubac\UserPermissionFunctionRepository;
use Exception;
use frontend\models\work\rubac\PermissionFunctionWork;
use frontend\models\work\rubac\PermissionTemplateWork;
use frontend\models\work\rubac\UserPermissionFunctionWork;
use Yii;
use yii\console\Controller;
use yii\rbac\Permission;

class RoleCopyController extends Controller
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
    public function actionCopyAll(){
        var_dump("OK!!!!");
    }
    public function actionDeleteAll(){

    }
    public function actionCopyUserPermission(){
        $users = Yii::$app->old_db->createCommand('SELECT * FROM user')->queryAll();
        foreach ($users as $user){
            $userId = $user['id'];
            $userRoles = Yii::$app->old_db->createCommand("SELECT * FROM user_role WHERE user_id = $userId")->queryAll();
            foreach ($userRoles as $userRole) {
                $newRolePermissions = $this->setNewRole($userRole['role_id']);
                foreach ($newRolePermissions as $newRolePermission) {
                    $model = new UserPermissionFunctionWork();
                    $model->user_id = $userId;
                    $model->function_id = (PermissionFunctionWork::find()->where(['short_code' => $newRolePermission])->one())->id;
                    $model->save();
                }
            }
        }
    }
    public function setNewRole($oldRole){
        switch ($oldRole){
            case 1:
                $permissions = Yii::$app->rubac->getTeacherPermissions();
                return $permissions;
            case 2:
                $permissions = Yii::$app->rubac->getStudyInformantPermissions();
                return $permissions;
            case 3:
                $permissions = Yii::$app->rubac->getEventInformantPermissions();
                return $permissions;
            case 4:
                $permissions = Yii::$app->rubac->getDocumentInformantPermissions();
                return $permissions;
            case 5:
                $permissions = Yii::$app->rubac->getBranchControllerPermissions();
                return $permissions;
            case 6:
                $permissions = Yii::$app->rubac->getSuperControllerPermissions();
                return $permissions;
            case 7:
                $permissions = Yii::$app->rubac->getAdminPermissions();
                return $permissions;
        }
        return [];
    }
}