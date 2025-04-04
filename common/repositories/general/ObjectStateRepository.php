<?php

namespace common\repositories\general;

use DomainException;
use frontend\models\work\ObjectStatesWork;

class ObjectStateRepository
{
    public function get($id)
    {
        return ObjectStatesWork::find()->where(['id' => $id])->one();
    }

    public function getByPolymorphKey($tableName, $rowId)
    {
        return ObjectStatesWork::find()->where(['table_name' => $tableName])->andWhere(['table_row_id' => $rowId])->one();
    }

    public function create(ObjectStatesWork $state)
    {
        if ($entity = $this->getByPolymorphKey($state->table_name, $state->table_row_id)) {
            return $entity->id;
        }

        return $this->save($state);
    }

    public function setReadState($id)
    {
        $entity = ObjectStatesWork::find()->where(['id' => $id])->one();
        /** @var ObjectStatesWork $entity */
        $entity->state = ObjectStatesWork::STATE_READ;
        $this->save($entity);
    }

    public function setWriteState($id)
    {
        $entity = ObjectStatesWork::find()->where(['id' => $id])->one();
        /** @var ObjectStatesWork $entity */
        $entity->state = ObjectStatesWork::STATE_WRITE;
        $this->save($entity);
    }

    public function setFreeState($id)
    {
        $entity = ObjectStatesWork::find()->where(['id' => $id])->one();
        /** @var ObjectStatesWork $entity */
        $entity->state = ObjectStatesWork::STATE_FREE;
        $this->save($entity);
    }

    public function save(ObjectStatesWork $state)
    {
        if (!$state->save()) {
            throw new DomainException('Ошибка сохранения пользователя. Проблемы: '.json_encode($state->getErrors()));
        }

        return $state->id;
    }
}