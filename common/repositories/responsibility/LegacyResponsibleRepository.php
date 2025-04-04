<?php

namespace common\repositories\responsibility;

use DomainException;
use frontend\models\work\responsibility\LegacyResponsibleWork;
use frontend\models\work\responsibility\LocalResponsibilityWork;

class LegacyResponsibleRepository
{
    public function get($id)
    {
        return LegacyResponsibleWork::find()->where(['id' => $id])->one();
    }

    /**
     * @param LocalResponsibilityWork $responsibility
     * @param int $type тип возвращаемого значения: 0 - массив, 1 - одиночный элемент
     * @param array $params дополнительные условия в запросе - "people" (поиск по человеку), "end" (поиск по пустой дате окончания)
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getByResponsibility(LocalResponsibilityWork $responsibility, int $type, $params = [])
    {
        $query = LegacyResponsibleWork::find()
            ->where(['responsibility_type' => $responsibility->responsibility_type])
            ->andWhere(['branch' => $responsibility->branch])
            ->andWhere(['auditorium_id' => $responsibility->auditorium_id])
            ->andWhere(['quant' => $responsibility->quant]);

        if (in_array('people', $params)) {
            $query = $query->andWhere(['people_stamp_id' => $responsibility->people_stamp_id]);
        }

        if (in_array('end', $params)) {
            $query = $query->andWhere(['IS', 'end_date', null]);
        }

        return $type == 0 ?
            $query->all() :
            $query->one();
    }

    public function save(LegacyResponsibleWork $legacy)
    {
        if (!$legacy->save()) {
            throw new DomainException('Ошибка сохранения истории ответственности. Проблемы: '.json_encode($legacy->getErrors()));
        }

        return $legacy->id;
    }

    public function delete(LegacyResponsibleWork $legacy)
    {
        if (!$legacy->delete()) {
            throw new DomainException('Ошибка удаления истории ответственности. Проблемы: '.json_encode($legacy->getErrors()));
        }
    }
}