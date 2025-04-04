<?php

namespace common\repositories\dictionaries;

use common\components\traits\CommonDatabaseFunctions;
use DomainException;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonalDataParticipantWork;
use Yii;

class PersonalDataParticipantRepository
{
    use CommonDatabaseFunctions;

    public function getPersonalDataByParticipantId($participantId)
    {
        return PersonalDataParticipantWork::find()->where(['participant_id' => $participantId])->all();
    }

    public function getPersonalDataRestrict($participantId)
    {
        return PersonalDataParticipantWork::find()->where(['participant_id' => $participantId])->all();
    }

    public function prepareDeleteAllPersonalDataByParticipant($participantId)
    {
        $commands = [];
        $pds = $this->getPersonalDataByParticipantId($participantId);

        foreach ($pds as $pd) {
            $command = Yii::$app->db->createCommand();
            $command->delete(
                PersonalDataParticipantWork::tableName(),
                ['id' => $pd->id]
            );

            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function prepareCreateNewPersonalData($participantId)
    {
        $commands = [];
        $pdIds = Yii::$app->personalData->customSort();
        foreach ($pdIds as $key => $pdId) {
            $model = PersonalDataParticipantWork::fill($participantId, $key, PersonalDataParticipantWork::STATUS_FREE);
            $command = Yii::$app->db->createCommand();
            $command->insert($model::tableName(), $model->getAttributes());
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function prepareResetAllPersonalData($participantId)
    {
        $commands = [];
        $pds = $this->getPersonalDataByParticipantId($participantId);
        if (count($pds) < count(Yii::$app->personalData->getList())) {
            $commands = array_merge($commands, $this->prepareDeleteAllPersonalDataByParticipant($participantId));
            $commands = array_merge($commands, $this->prepareCreateNewPersonalData($participantId));
        }
        else {
            foreach ($pds as $pd) {
                $command = Yii::$app->db->createCommand();
                $command->update(
                    PersonalDataParticipantWork::tableName(),
                    ['status' => PersonalDataParticipantWork::STATUS_FREE],
                    ['id' => $pd->id]
                );

                $commands[] = $command->getRawSql();
            }
        }

        return $commands;
    }

    public function prepareAttachPersonalData($participantId, $pd)
    {
        $commands = [];
        foreach ($pd as $one) {
            $command = Yii::$app->db->createCommand();
            $command->update(
                PersonalDataParticipantWork::tableName(),
                ['status' => PersonalDataParticipantWork::STATUS_RESTRICT],
                ['participant_id' => $participantId, 'personal_data' => $one]
            );
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function prepareDetachPersonalData($participantId)
    {
        $pds = $this->getPersonalDataByParticipantId($participantId);

        $commands = [];
        foreach ($pds as $one) {
            $command = Yii::$app->db->createCommand();
            $command->delete(
                PersonalDataParticipantWork::tableName(),
                ['id' => $one->id]
            );
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function delete(PersonalDataParticipantWork $model)
    {
        return $model->delete();
    }

    public function save(PersonalDataParticipantWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения ограничения персональных данных. Проблемы: '.json_encode($model->getErrors()));
        }

        return $model->id;
    }
}