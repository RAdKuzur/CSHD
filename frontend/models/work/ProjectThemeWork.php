<?php

namespace frontend\models\work;

use common\models\scaffold\ProjectTheme;
use Yii;

class ProjectThemeWork extends ProjectTheme
{
    public static function fill(string $name, int $projectType, string $description, int $id = null)
    {
        $entity = new static();
        $entity->id = $id;
        $entity->name = $name;
        $entity->project_type = $projectType;
        $entity->description = $description;

        return $entity;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['id', 'integer'],
            [['name', 'project_type'], 'required']
        ]);
    }

    public function getProjectTypeString()
    {
        return Yii::$app->projectType->get($this->project_type);
    }
}
