<?php

namespace common\repositories\dictionaries;

use DomainException;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use yii\helpers\ArrayHelper;

class PositionRepository
{
    public function get($id)
    {
        return PositionWork::find()->where(['id' => $id])->one();
    }

    /**
     * Возвращает список должностей
     * @param int|null $peopleId если передан параметр, то возвращает список должностей человека @see PeoplePositionBranchWork
     * @return array
     */
    public function getList(int $peopleId = null): array
    {
        $query = PositionWork::find();
        if ($peopleId) {
            $subQuery = PeoplePositionCompanyBranchWork::find()->where(['people_id' => $peopleId])->all();
            $query->andWhere(['IN', 'id', ArrayHelper::getColumn($subQuery, 'position_id')]);
        }

        return $query->all();
    }

    public function delete(PositionWork $position)
    {
        if (!$position->delete()) {
            throw new DomainException('Ошибка удаления организации. Проблемы: '.json_encode($position->getErrors()));
        }

        return $position->id;
    }

    public function save(PositionWork $position)
    {
        if (!$position->save()) {
            throw new DomainException('Ошибка привязки правила к пользователю. Проблемы: '.json_encode($position->getErrors()));
        }

        return $position->id;
    }
}