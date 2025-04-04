<?php

namespace common\repositories\educational;

use DomainException;
use frontend\models\work\ProjectThemeWork;
use Yii;

class ProjectThemeRepository
{
    public function get($id)
    {
        return ProjectThemeWork::find()->where(['id' => $id])->one();
    }

    public function getByParams(string $name, int $projectType, string $description)
    {
        return ProjectThemeWork::find()->where(['name' => $name])->andWhere(['project_type' => $projectType])->andWhere(['description' => $description])->one();
    }

    public function getThemes(array $ids)
    {
        return ProjectThemeWork::find()->where(['IN', 'id', $ids])->all();
    }

    public function prepareUpdate($id, $projectType, $description)
    {
        $command = Yii::$app->db->createCommand();
        $command->update('project_theme', ['project_type' => $projectType, 'description' => $description], "id = $id");
        return $command->getRawSql();
    }

    public function save(ProjectThemeWork $theme)
    {
        /** @var ProjectThemeWork|null $duplicate */
        $duplicate = $this->getByParams($theme->name, $theme->project_type, $theme->description);
        if (!$duplicate) {
            if (!$theme->save()) {
                throw new DomainException('Ошибка сохранения темы проекта. Проблемы: '.json_encode($theme->getErrors()));
            }
            return $theme->id;
        }
        return $duplicate->id;
    }
}