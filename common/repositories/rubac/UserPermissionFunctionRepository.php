<?php

namespace common\repositories\rubac;

use DomainException;
use frontend\models\work\rubac\PermissionFunctionWork;
use frontend\models\work\rubac\PermissionTemplateWork;
use frontend\models\work\rubac\PermissionTokenWork;
use frontend\models\work\rubac\UserPermissionFunctionWork;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class UserPermissionFunctionRepository
{
    private PermissionFunctionRepository $permissionFunctionRepository;

    public function __construct(PermissionFunctionRepository $permissionFunctionRepository)
    {
        $this->permissionFunctionRepository = $permissionFunctionRepository;
    }

    public function attachTemplatePermissionsToUser($templateName, $userId, $branch)
    {
        if ($branch == "") {
            $branch = null;
        }

        if (array_key_exists($templateName, PermissionTemplateWork::getTemplateNames()) &&
            (array_key_exists($branch, Yii::$app->branches->getList()) || $branch == null)) {
            $functions = $this->permissionFunctionRepository->getTemplateLinkedPermissions($templateName);

            foreach ($functions as $function) {
                $this->save(
                    UserPermissionFunctionWork::fill(
                        $userId,
                        $function->id,
                        Yii::$app->branches->get($branch)
                    )
                );
            }

            return true;
        }

        throw new NotFoundHttpException("Неизвестный тип шаблона - $templateName или неизвестный отдел - $branch");
    }

    /**
     * Возвращает список PermissionFunctionWork для пользователя с ID = userId
     * @param $userId
     * @return PermissionFunctionWork[]
     */
    public function getPermissionsByUser($userId)
    {
        $userPermissions = ArrayHelper::getColumn(UserPermissionFunctionWork::find()->where(['user_id' => $userId])->all(), 'function_id');
        $userPermissionTokens = ArrayHelper::getColumn(PermissionTokenWork::find()->where(['user_id' => $userId])->all(), 'function_id');
        return PermissionFunctionWork::find()->where(['IN', 'id', array_unique(array_merge($userPermissions, $userPermissionTokens))])->all();
    }

    public function getByUserPermissionBranch($userId, $permissionId, $branch = null)
    {
        $query = UserPermissionFunctionWork::find()
            ->where(['user_id' => $userId])
            ->andWhere(['function_id' => $permissionId]);

        if (!is_null($branch)) {
            $query = $query->andWhere(['branch' => $branch]);
        }
        else {
            $query = $query->andWhere(['IS', 'branch', null]);
        }

        return $query->one();
    }

    public function prepareCreate($userId, $functionId){
        $model = UserPermissionFunctionWork::fill($userId, $functionId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($userId, $functionId){
        $model = $this->getByUserPermissionBranch($userId, $functionId);
        if ($model) {
            $command = Yii::$app->db->createCommand();
            $command->delete($model::tableName(), $model->getAttributes());
            return $command->getRawSql();
        }

        return '';
    }

    public function save(UserPermissionFunctionWork $userFunction)
    {
        if (!$userFunction->save()) {
            throw new DomainException('Ошибка привязки правила к пользователю. Проблемы: '.json_encode($userFunction->getErrors()));
        }

        return $userFunction->id;
    }

    public function delete(PermissionFunctionWork $model)
    {
        return $model->delete();
    }
}