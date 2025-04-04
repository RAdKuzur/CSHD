<?php

namespace common\repositories\act_participant;

use common\components\logger\base\LogInterface;
use common\components\logger\LogFactory;
use frontend\models\work\team\ActParticipantBranchWork;
use Yii;
use yii\helpers\ArrayHelper;

class ActParticipantBranchRepository
{
    public function getEventIdsByBranches(array $branches)
    {
        $actParticipantsQuery = ActParticipantBranchWork::find()
            ->joinWith(['actParticipantWork actParticipantWork'])
            ->where(['IN', 'branch', $branches]);

        LogFactory::createCrudLog(LogInterface::LVL_INFO, 'Выгрузка уникальных актов участия по отделу', $actParticipantsQuery->createCommand()->getRawSql());

        return array_unique(
            ArrayHelper::getColumn(
                $actParticipantsQuery->all(),
                'actParticipantWork.foreign_event_id'
            )
        );
    }

    public function prepareCreate($actParticipantId, $branch)
    {
        $model = ActParticipantBranchWork::fill($actParticipantId, $branch);
        $model->save();
        return $model->id;
    }
    public function prepareDeleteByAct($actParticipantId)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(ActParticipantBranchWork::tableName(), ['act_participant_id' => $actParticipantId]);
        return $command->getRawSql();
    }
}