<?php

namespace common\repositories\educational;



use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use Yii;

class OrderTrainingGroupParticipantRepository
{
    public function get($id)
    {
        return OrderTrainingGroupParticipantWork::find()->where(['id' => $id])->one();
    }

    public function getEnrollByGroupId($groupId)
    {
        return OrderTrainingGroupParticipantWork::find()
            ->joinWith(['trainingGroupParticipantInWork trainingGroupParticipantInWork'])
            ->where(['trainingGroupParticipantInWork.training_group_id' => $groupId])
            ->all();
    }

    public function getExlusionByGroupId($groupId)
    {
        return OrderTrainingGroupParticipantWork::find()
            ->joinWith(['trainingGroupParticipantOutWork trainingGroupParticipantOutWork'])
            ->where(['trainingGroupParticipantOutWork.training_group_id' => $groupId])
            ->all();
    }

    public function prepareCreate(
        $trainingGroupParticipantOutId,
        $trainingGroupParticipantInId,
        $orderId
    ){
        $model = OrderTrainingGroupParticipantWork::fill(
            $trainingGroupParticipantOutId,
            $trainingGroupParticipantInId,
            $orderId
        );
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($trainingGroupParticipantOutId, $trainingGroupParticipantInId, $orderId)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(OrderTrainingGroupParticipantWork::tableName(),
            [
                'training_group_participant_in_id' => $trainingGroupParticipantInId,
                'order_id' => $orderId,
                'training_group_participant_out_id' => $trainingGroupParticipantOutId
            ]);
        return $command->getRawSql();
    }

    public function prepareDeleteById($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(OrderTrainingGroupParticipantWork::tableName(), [
            'id' => $id,
        ]);
        return $command->getRawSql();
    }

    public function getUnique($trainingGroupParticipantOutId, $orderId)
    {
        return OrderTrainingGroupParticipantWork::find()
            //->andWhere(['training_group_participant_in_id' => $trainingGroupParticipantInId])
            ->andWhere(['training_group_participant_out_id' => $trainingGroupParticipantOutId])
            ->andWhere(['order_id' => $orderId])
            ->one();
    }

    public function countByTrainingGroupParticipantOutId($trainingGroupParticipantOutId)
    {
        return OrderTrainingGroupParticipantWork::find()->where(['training_group_participant_out_id' => $trainingGroupParticipantOutId])->count();
    }

    public function getByOrderIds($id)
    {
        return OrderTrainingGroupParticipantWork::find()->where(['order_id' => $id])->all();
    }

    public function getExceptById($id)
    {
        return OrderTrainingGroupParticipantWork::find()->where(['<>', 'order_id' , $id])->all();
    }
}