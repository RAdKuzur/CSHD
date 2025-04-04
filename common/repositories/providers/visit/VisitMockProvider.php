<?php

namespace common\repositories\providers\visit;

use frontend\models\work\educational\journal\VisitWork;

class VisitMockProvider implements VisitProviderInterface
{
    public $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get($id)
    {
        return $this->data[$id] ?? null;
    }

    public function getByTrainingGroupParticipant(int $trainingGroupParticipantId)
    {
        return array_filter($this->data, function($item) use ($trainingGroupParticipantId) {
            return $item['training_group_participant_id'] === $trainingGroupParticipantId;
        });
    }

    public function getByTrainingGroupParticipants(array $trainingGroupParticipantIds)
    {
        return array_values(
            array_filter($this->data, function ($item) use ($trainingGroupParticipantIds) {
                return in_array($item['training_group_participant_id'], $trainingGroupParticipantIds, true);
            })
        );
    }

    public function delete(VisitWork $model)
    {
        unset($this->data[$model->id]);
        return true;
    }

    public function save(VisitWork $model)
    {
        $model->id = count($this->data);
        $this->data[] = $model;
        return $model->id;
    }
}