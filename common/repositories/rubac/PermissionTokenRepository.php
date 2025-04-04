<?php

namespace common\repositories\rubac;

use common\helpers\common\SqlHelper;
use DomainException;
use frontend\models\work\rubac\PermissionTokenWork;
use Yii;
use yii\db\Exception;

class PermissionTokenRepository
{
    public function get(int $id)
    {
        return PermissionTokenWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return PermissionTokenWork::find()->orderBy(['end_time' => SORT_ASC])->all();
    }

    public function findByUserFunctionBranch(int $userId, int $functionId, int $branch = null)
    {
        $query = PermissionTokenWork::find()
            ->where(['user_id' => $userId])
            ->andWhere(['function_id' => $functionId]);

        if (!is_null($branch)) {
            $query = $query->andWhere(['branch' => $branch]);
        }
        else {
            $query = $query->andWhere(['IS', 'branch', null]);
        }

        return $query->one();
    }

    public function delete(PermissionTokenWork $model)
    {
        return $model->delete();
    }

    /**
     * @throws Exception
     */
    public function save(PermissionTokenWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка создания токена доступа для пользователя. Проблемы: '.json_encode($model->getErrors()));
        }

        return $model->id;
    }
    public function isPossibleInsert($userId, $functionId)
    {
        $currentTime = date('Y-m-d H:i:s');
        return PermissionTokenWork::find()
            ->where(['user_id' => $userId])
            ->andWhere(['function_id' => $functionId])
            ->andWhere(['<', 'start_time' , $currentTime])
            ->andWhere(['>','end_time' , $currentTime])
            ->exists();
    }
    public function getActiveToken($userId)
    {
        $currentTime = date('Y-m-d H:i:s');
        $query = PermissionTokenWork::find()
            ->where(['user_id' => $userId])
            ->andWhere(['<', 'start_time' , $currentTime])
            ->andWhere(['>','end_time' , $currentTime]);
        return $query->all();
    }
}