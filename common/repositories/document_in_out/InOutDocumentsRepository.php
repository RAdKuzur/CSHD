<?php

namespace common\repositories\document_in_out;

use DomainException;
use frontend\models\work\document_in_out\InOutDocumentsWork;
use Yii;

class InOutDocumentsRepository
{
    /**
     * Возвращает строку по ID
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function get($id)
    {
        return InOutDocumentsWork::find()->where(['id' => $id])->one();
    }

    /**
     * Добавляет новую запись в таблицу, возвращает ID новой записи
     * @param $docInId
     * @param $docOutId
     * @param $date
     * @param $responsibleId
     * @return int
     */
    public function create($docInId, $docOutId = null, $date = null, $responsibleId = null)
    {
        $entity = InOutDocumentsWork::fill($docInId, $docOutId, $date, $responsibleId);
        return $this->save($entity);
    }

    /**
     * Возвращает строку по ID входящего документа
     * @param $docInId
     * @return array|\yii\db\ActiveRecord|null
     */
    public function getByDocumentInId($docInId)
    {
        return InOutDocumentsWork::find()->where(['document_in_id' => $docInId])->one();
    }
    public function getByDocumentOutId($docOutId)
    {
        return InOutDocumentsWork::find()->where(['document_out_id' => $docOutId])->one();
    }
    /**
     * Подготавливает запрос для создания новой записи в таблице
     * @param $docInId
     * @param $docOutId
     * @param $date
     * @param $responsibleId
     * @return string
     */
    public function prepareCreate($docInId, $docOutId = null, $date = null, $responsibleId = null)
    {
        $model = InOutDocumentsWork::fill($docInId, $docOutId, $date, $responsibleId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareUpdate($docInId, $docOutId = null, $date = null, $responsibleId = null)
    {
        /** @var InOutDocumentsWork $model */
        $model = $this->getByDocumentInId($docInId);
        if ($model === null) {
            throw new DomainException("Не найдена запись в in_out_documents для document_in {$docInId}");
        }

        $model->document_in_id = $docInId;
        $model->document_out_id = $docOutId;
        $model->date = $date;
        $model->responsible_id = $responsibleId;

        $command = Yii::$app->db->createCommand();
        $command->update($model::tableName(), $model->getAttributes(), ['document_in_id' => $docInId]);
        return $command->getRawSql();
    }

    public function prepareDelete($docInId)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(InOutDocumentsWork::tableName(), ['document_in_id' => $docInId]);
        return $command->getRawSql();
    }

    /**
     * Сохраняет запись в таблице
     * @param InOutDocumentsWork $user
     * @return int
     * @throws \yii\db\Exception
     */
    public function save(InOutDocumentsWork $document)
    {
        if (!$document->save()) {
            throw new DomainException('Ошибка сохранения связки входящий/исходящий документы. Проблемы: '.json_encode($document->getErrors()));
        }
        return $document->id;
    }
}