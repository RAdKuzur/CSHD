<?php

namespace common\repositories\act_participant;

use common\components\logger\base\LogInterface;
use common\components\logger\LogFactory;
use DomainException;
use frontend\models\work\team\SquadParticipantWork;
use common\models\scaffold\SquadParticipant;
use Yii;
use function PHPUnit\Framework\throwException;

class SquadParticipantRepository
{
    public function prepareCreate($actParticipantId, $participantId)
    {
        $model = SquadParticipantWork::fill($actParticipantId, $participantId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($actParticipantId, $participantId)
    {
        $model = SquadParticipantWork::find()
            ->andWhere(['act_participant_id' => $actParticipantId])
            ->andWhere(['participant_id' => $participantId])
            ->one();
        $command = Yii::$app->db->createCommand();
        $command->delete($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
    public function prepareDeleteByActId($actId)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(SquadParticipantWork::tableName(), ['act_participant_id' => $actId]);
        return $command->getRawSql();
    }

    public function getAllByParticipantId($participantId)
    {
        $query = SquadParticipantWork::find()->where(['participant_id' => $participantId]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка всех записей squad_participant по ID участника деятельности', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function getCountByActAndParticipantId($actId, $participantId)
    {
        $query = SquadParticipantWork::find()->andWhere(['act_participant_id' => $actId, 'participant_id' => $participantId]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка количества записей squad_participant по ID акта участия и ID участника деятельности', $query->createCommand()->getRawSql());
        return $query->count();
    }

    public function getAllByActId($actId)
    {
        $query = SquadParticipantWork::find()->andWhere(['act_participant_id' => $actId]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка всех записей squad_participant по ID акта участия', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function getAllByActIds(array $actIds)
    {
        $query = SquadParticipantWork::find()->andWhere(['IN', 'act_participant_id', $actIds]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка всех записей squad_participant по ID акта участия', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function getAllFromEvent($foreignEventId)
    {
        $query = SquadParticipantWork::find()->joinWith(['actParticipantWork actParticipantWork'])->where(['actParticipantWork.foreign_event_id' => $foreignEventId]);
        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка всех записей squad_participant по ID мероприятия', $query->createCommand()->getRawSql());
        return $query->all();
    }

    public function save(SquadParticipantWork $model)
    {
        if ($model->isNewRecord) {
            $sql = Yii::$app->db->createCommand()->insert($model->tableName(), $model->attributes)->getRawSql();
        } else {
            $sql = Yii::$app->db->createCommand()->update($model->tableName(), $model->attributes, ['id' => $model->id])->getRawSql();
        }

        if (!$model->save()) {
            LogFactory::createCrudLog(LogInterface::LVL_ERROR, 'Ошибка сохранения записи squad_participant', $sql);
            throw new DomainException('Ошибка сохранения. Проблемы: '.json_encode($model->getErrors()));
        }

        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Сохранение записи squad_participant', $sql);
        return $model->id;
    }
}