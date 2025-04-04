<?php

namespace common\repositories\providers\group_expert;

use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use Yii;

class TrainingGroupExpertProvider implements TrainingGroupExpertProviderInterface
{
    public function get($id)
    {
        return TrainingGroupExpertWork::find()->where(['id' => $id])->one();
    }

    public function getExpertsFromGroup($groupId, $type)
    {
        return TrainingGroupExpertWork::find()->where(['training_group_id' => $groupId])->andWhere(['expert_type' => $type])->all();
    }

    public function prepareCreate($groupId, $expertId, $expertType)
    {
        $model = TrainingGroupExpertWork::fill($groupId, $expertId, $expertType);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(TrainingGroupExpertWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function prepareUpdate($id, $expertId, $expertType)
    {
        $command = Yii::$app->db->createCommand();
        $command->update('training_group_expert', ['expert_id' => $expertId, 'expert_type' => $expertType], "id = $id");
        return $command->getRawSql();
    }

    public function save(TrainingGroupExpertWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения связки учебной группы и эксперта. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}