<?php

namespace common\repositories\providers\group_participant;

use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;

class TrainingGroupParticipantProvider implements TrainingGroupParticipantProviderInterface
{
    public function get($id)
    {
        return TrainingGroupParticipantWork::find()->where(['id' => $id])->one();
    }

    public function getByIds(array $ids)
    {
        return TrainingGroupParticipantWork::find()->where(['IN', 'id', $ids])->all();
    }

    public function getParticipantsFromGroups(array $groupId)
    {
        return TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groupId])->all();
    }

    public function getSuccessParticipantsFromGroup(int $groupId)
    {
        return TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groupId])->andWhere(['success' => 1])->all();
    }

    public function getByParticipantIds(array $ids)
    {
        return TrainingGroupParticipantWork::find()->where(['IN', 'participant_id', $ids])->all();
    }

    public function getByParticipantIdAndGroupId(int $participantId, int $groupId)
    {
        return TrainingGroupParticipantWork::find()->where(['participant_id' => $participantId])->andWhere(['training_group_id' => $groupId])->one();
    }

    public function prepareCreate($groupId, $participantId, $sendMethod)
    {
        $model = TrainingGroupParticipantWork::fill($groupId, $participantId, $sendMethod);
        $model->success = false;
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(TrainingGroupParticipantWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function prepareUpdate($id, $participantId, $sendMethod)
    {
        $command = Yii::$app->db->createCommand();
        $command->update('training_group_participant', ['participant_id' => $participantId, 'send_method' => $sendMethod], "id = $id");
        return $command->getRawSql();
    }

    public function delete(TrainingGroupParticipantWork $model)
    {
        return $model->delete();
    }

    public function save(TrainingGroupParticipantWork $participant)
    {
        if (!$participant->save()) {
            throw new DomainException('Ошибка сохранения связки учебной группы и ученика. Проблемы: '.json_encode($participant->getErrors()));
        }
        return $participant->id;
    }
}