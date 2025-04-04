<?php


namespace common\repositories\event;


use DomainException;
use frontend\forms\event\ParticipantAchievementForm;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\team\ActParticipantWork;
use Yii;

class ParticipantAchievementRepository
{
    public function get($id)
    {
        return ParticipantAchievementWork::find()->where(['id' => $id])->one();
    }

    public function getByParticipantId($participantId)
    {
        return ParticipantAchievementWork::find()
            ->joinWith(['actParticipantWork actParticipantWork'])
            ->joinWith(['actParticipantWork.squadParticipantWork squadParticipantWork'])
            ->where(['squadParticipantWork.participant_id' => $participantId])->all();
    }

    public function getByForeignEvent(
        int $foreignEventId,
        array $prizeTypes = [ParticipantAchievementWork::TYPE_PRIZE, ParticipantAchievementWork::TYPE_WINNER],
        array $participantTypes = [ActParticipantWork::TYPE_SOLO, ActParticipantWork::TYPE_TEAM]
    )
    {
        return ParticipantAchievementWork::find()
            ->joinWith(['actParticipantWork actParticipantWork'])
            ->where(['actParticipantWork.foreign_event_id' => $foreignEventId])
            ->andWhere(['IN', 'participant_achievement.type', $prizeTypes])
            ->andWhere(['IN', 'actParticipantWork.type', $participantTypes])
            ->all();
    }

    public function getByTeacherId($teacherId)
    {
        return ParticipantAchievementWork::find()
            ->joinWith(['actParticipantWork actParticipantWork'])
            ->where(['actParticipantWork.teacher_id' => $teacherId])
            ->orWhere(['actParticipantWork.teacher2_id' => $teacherId])
            ->all();
    }

    public function prepareCreate($actParticipantId, $achievement, $type, $certNumber, $nomination, $date)
    {
        $model = ParticipantAchievementWork::fill($actParticipantId, $achievement, $type, $certNumber, $nomination, $date);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function save(ParticipantAchievementWork $achievement)
    {
        if (!$achievement->save()) {
            throw new DomainException('Ошибка сохранения положения. Проблемы: '.json_encode($achievement->getErrors()));
        }

        return $achievement->id;
    }

    public function delete(ParticipantAchievementWork $achievement)
    {
        return $achievement->delete();
    }
}