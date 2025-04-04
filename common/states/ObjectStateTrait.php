<?php

namespace common\states;

use common\repositories\general\ObjectStateRepository;

trait ObjectStateTrait
{
    private ObjectStateRepository $repository;

    public function setRead($id)
    {
        $this->repository->setReadState($id);
    }

    public function setWrite($id)
    {
        $this->repository->setWriteState($id);
    }

    public function drop($id)
    {
        $this->repository->setFreeState($id);
    }

    public function createState(StateInterface $entity)
    {
        return $this->repository->create($entity);
    }
}