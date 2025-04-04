<?php

namespace common\repositories\order;
use frontend\models\work\order\OrderTrainingWork;
use frontend\services\order\OrderTrainingService;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use DomainException;

class OrderTrainingRepository
{
    public TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    public TrainingGroupRepository $trainingGroupRepository;
    public OrderTrainingService $orderTrainingService;
    public function __construct(
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        TrainingGroupRepository $trainingGroupRepository,
        OrderTrainingService $orderTrainingService
    )
    {
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->orderTrainingService = $orderTrainingService;
    }

    public function get($id)
    {
        return OrderTrainingWork::findOne($id);
    }
    public function save(OrderTrainingWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения документа. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }

    /**
     * Поиск всех приказов по группе
     * @param int $idGroup
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllByGroup(int $idGroup)
    {
        return OrderTrainingWork::find()
                    ->joinWith([
                        'orderTrainingGroupParticipantWork' => function ($query) {
                            $query->joinWith('trainingGroupParticipantInWork', true, 'INNER JOIN')
                            ->joinWith('trainingGroupParticipantOutWork', true, 'INNER JOIN');
                        }
                    ], true, 'INNER JOIN')
                    ->where(['training_group_participant.training_group_id' => $idGroup])
                    ->groupBy('id')
                    ->all();
    }
}