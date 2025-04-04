<?php

namespace frontend\models\work\rubac;

use common\models\scaffold\PermissionTemplateFunction;

class PermissionTemplateFunctionWork extends PermissionTemplateFunction
{
    public static function fill($templateId, $functionId)
    {
        $entity = new static();
        $entity->function_id = $functionId;
        $entity->template_id = $templateId;

        return $entity;
    }
}
