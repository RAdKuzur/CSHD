<?php

namespace common\repositories\rubac;

use DomainException;
use frontend\models\work\rubac\PermissionFunctionWork;
use frontend\models\work\rubac\PermissionTemplateFunctionWork;
use frontend\models\work\rubac\PermissionTemplateWork;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class PermissionFunctionRepository
{
    /**
     * Возвращает все связанные с шаблоном правила или NotFoundHttpException
     * @param $templateName
     * @return array|\yii\db\ActiveRecord[]
     * @throws NotFoundHttpException
     */
    public function getTemplateLinkedPermissions($templateName)
    {
        $templateId = PermissionTemplateWork::find()->where(['name' => $templateName])->one()->id;
        $functionsId = ArrayHelper::getColumn(PermissionTemplateFunctionWork::find()->where(['template_id' => $templateId])->all(), 'function_id');
        return PermissionFunctionWork::find()->where(['IN', 'id', $functionsId])->all();
    }

    public function getAllPermissions()
    {
        return PermissionFunctionWork::find()->orderBy(['name' => SORT_ASC])->all();
    }

    public function save(PermissionFunctionWork $function)
    {
        if (!$function->save()) {
            throw new DomainException('Ошибка привязки правила к пользователю. Проблемы: '.json_encode($function->getErrors()));
        }

        return $function->id;
    }
}