<?php

namespace common\repositories\providers\group_participant;

use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;

class TrainingGroupParticipantMockProvider implements TrainingGroupParticipantProviderInterface
{
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get($id)
    {
        return $this->data[$id] ?? null;
    }

    public function getByIds(array $ids)
    {
        return array_filter($this->data, function($key) use ($ids) {
            return in_array($key, $ids);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getParticipantsFromGroups(array $groupId)
    {
        return array_filter($this->data, function($item) use ($groupId) {
            return in_array($item['training_group_id'], $groupId);
        });
    }

    public function getSuccessParticipantsFromGroup(int $groupId)
    {
        return array_filter($this->data, function($item) use ($groupId) {
            return
                $item['success'] == 1 &&
                $item['training_group_id'] == $groupId;
        });
    }

    public function getByParticipantIds(array $ids)
    {
        return array_filter($this->data, function ($participant) use ($ids) {
            return in_array($participant->participant_id, $ids);
        });
    }

    public function delete(TrainingGroupParticipantWork $model)
    {
        unset($this->data[$model->id]);
        return true;
    }

    public function save(TrainingGroupParticipantWork $model)
    {
        $model->id = count($this->data);
        $this->data[] = $model;
        return $model->id;
    }
}