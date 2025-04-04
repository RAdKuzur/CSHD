<?php

namespace frontend\models\work\rubac;

use common\models\scaffold\PermissionTemplate;

class PermissionTemplateWork extends PermissionTemplate
{
    const TEMPLATE_TEACHER = 'teacher';
    const TEMPLATE_STUDY_INFO = 'study_info';
    const TEMPLATE_EVENT_INFO = 'event_info';
    const TEMPLATE_DOC_INFO = 'doc_info';
    const TEMPLATE_MATERIAL_INFO = 'material_info';
    const TEMPLATE_BRANCH_CONTROLLER = 'branch_controller';
    const TEMPLATE_SUPER_CONTROLLER = 'super_controller';
    const TEMPLATE_ADMIN = 'admin';

    public static function getTemplateNames()
    {
        return [
            self::TEMPLATE_TEACHER => 'Педагог',
            self::TEMPLATE_STUDY_INFO => 'Информатор по учебной деятельности в отделе',
            self::TEMPLATE_EVENT_INFO => 'Информатор по мероприятиям в отделе',
            self::TEMPLATE_DOC_INFO => 'Информатор по документообороту',
            self::TEMPLATE_MATERIAL_INFO => 'Информатор по материальным ценностям',
            self::TEMPLATE_BRANCH_CONTROLLER => 'Контролер в отделе',
            self::TEMPLATE_SUPER_CONTROLLER => 'Суперконтролер',
            self::TEMPLATE_ADMIN => 'Администратор',
        ];
    }

    public static function fill($name, $id = null)
    {
        $entity = new static();
        if ($id) {
            $entity->id = $id;
        }
        $entity->name = $name;

        return $entity;
    }
}
