<?php

namespace common\repositories\providers\group_expert;

use frontend\models\work\educational\training_group\TrainingGroupExpertWork;

class TrainingGroupExpertMockProvider implements TrainingGroupExpertProviderInterface
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

    public function getExpertsFromGroup($groupId, $type)
    {
        return array_filter($this->data, function($item) use ($groupId, $type) {
            return
                in_array($item['type'], $type) &&
                $item['training_group_id'] === $groupId;
        });
    }

    public function save(TrainingGroupExpertWork $model)
    {
        $model->id = count($this->data);
        $this->data[] = $model;
        return $model->id;
    }
}