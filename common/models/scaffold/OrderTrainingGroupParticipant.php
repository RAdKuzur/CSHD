<?php

namespace common\models\scaffold;

use common\models\scaffold\TrainingGroupParticipant;
use Yii;

/**
 * This is the model class for table "order_training_group_participant".
 *
 * @property int $id
 * @property int|null $training_group_participant_out_id
 * @property int|null $training_group_participant_in_id
 * @property int $order_id
 *
 * @property DocumentOrder $order
 * @property TrainingGroupParticipant $trainingGroupParticipantIn
 * @property TrainingGroupParticipant $trainingGroupParticipantOut
 */
class OrderTrainingGroupParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_training_group_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_group_participant_out_id', 'training_group_participant_in_id', 'order_id'], 'integer'],
            [['order_id'], 'required'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::class, 'targetAttribute' => ['order_id' => 'id']],
            [['training_group_participant_in_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupParticipant::class, 'targetAttribute' => ['training_group_participant_in_id' => 'id']],
            [['training_group_participant_out_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupParticipant::class, 'targetAttribute' => ['training_group_participant_out_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_group_participant_out_id' => 'Training Group Participant Out ID',
            'training_group_participant_in_id' => 'Training Group Participant In ID',
            'order_id' => 'Order ID',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(DocumentOrder::class, ['id' => 'order_id']);
    }

    /**
     * Gets query for [[TrainingGroupParticipantIn]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipantIn()
    {
        return $this->hasOne(TrainingGroupParticipant::class, ['id' => 'training_group_participant_in_id']);
    }

    /**
     * Gets query for [[TrainingGroupParticipantOut]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipantOut()
    {
        return $this->hasOne(TrainingGroupParticipant::class, ['id' => 'training_group_participant_out_id']);
    }
}