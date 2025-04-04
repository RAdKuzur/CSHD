<?php

namespace common\repositories\event;

use frontend\models\work\event\ForeignEventWork;
use DomainException;
use Yii;

class ForeignEventRepository
{
    public function get($id)
    {
        return ForeignEventWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return ForeignEventWork::find()->all();
    }

    public function getByIds(array $ids)
    {
        return ForeignEventWork::find()->where(['IN', 'id', $ids])->all();
    }

    public function getByDocOrderId($id)
    {
        return ForeignEventWork::find()->where(['order_participant_id' => $id])->one();
    }

    /**
     * Возвращает все мероприятия, завершившиеся в промежуток [$startDate; $endDate]
     * и соответствующие уровням из $levels
     *
     * @param string $startDate
     * @param string $endDate
     * @param array $levels
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getByDatesAndLevels(string $startDate, string $endDate, array $levels = [])
    {
        $query = ForeignEventWork::find()->where(['>=', 'end_date', $startDate])->andWhere(['<=', 'end_date', $endDate]);
        if (count($levels) > 0) {
            $query = $query->andWhere(['IN', 'level', $levels]);
        }

        return $query->all();
    }

    public function delete($id)
    {
        return ForeignEventWork::deleteAll(['id' => $id]);
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(ForeignEventWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function save(ForeignEventWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }

    public function existByOrderId($orderId){
        return ForeignEventWork::find()->where(['order_participant_id' => $orderId])->exists();
    }
}