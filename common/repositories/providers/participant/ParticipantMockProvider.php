<?php

namespace common\repositories\providers\participant;

use frontend\models\work\dictionaries\ForeignEventParticipantsWork;

class ParticipantMockProvider implements ParticipantProviderInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get($id)
    {
        return $this->data[$id] ?? null;
    }

    public function getParticipants(array $ids)
    {
        return array_intersect_key($this->data, array_flip($ids));
    }

    public function delete(ForeignEventParticipantsWork $model)
    {
        unset($this->data[$model->id]);
        return true;
    }

    public function save(ForeignEventParticipantsWork $model)
    {
        $model->id = count($this->data);
        $this->data[] = $model;
        return $model->id;
    }
}