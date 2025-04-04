<?php

namespace common\repositories\providers\group_project_themes;

use DomainException;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use Yii;

class GroupProjectThemesProvider implements GroupProjectThemesProviderInterface
{
    public function get($id)
    {
        return GroupProjectThemesWork::find()->where(['id' => $id])->one();
    }

    public function getProjectThemesFromGroup($groupId)
    {
        return GroupProjectThemesWork::find()->joinWith(['projectThemeWork'])->where(['training_group_id' => $groupId])->all();
    }

    public function prepareCreate($groupId, $themeId, $confirm)
    {
        $model = GroupProjectThemesWork::fill($groupId, $themeId, $confirm);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(GroupProjectThemesWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function save(GroupProjectThemesWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения связки учебной группы и темы проекта. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}