<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "in_out_documents".
 *
 * @property int $id
 * @property int $document_in_id
 * @property int $document_out_id
 * @property string|null $date
 * @property int|null $responsible_id
 *
 * @property DocumentIn $documentIn
 * @property DocumentOut $documentOut
 * @property PeopleStamp $responsible
 */
class InOutDocuments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'in_out_documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_in_id'], 'required'],
            [['document_in_id', 'document_out_id', 'responsible_id'], 'integer'],
            [['date'], 'safe'],
            [['document_in_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentIn::class, 'targetAttribute' => ['document_in_id' => 'id']],
            [['document_out_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOut::class, 'targetAttribute' => ['document_out_id' => 'id']],
            [['responsible_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['responsible_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_in_id' => 'Document In ID',
            'document_out_id' => 'Document Out ID',
            'date' => 'Date',
            'responsible_id' => 'Responsible ID',
        ];
    }

    /**
     * Gets query for [[DocumentIn]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentIn()
    {
        return $this->hasOne(DocumentIn::class, ['id' => 'document_in_id']);
    }

    /**
     * Gets query for [[DocumentOut]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOut()
    {
        return $this->hasOne(DocumentOut::class, ['id' => 'document_out_id']);
    }

    /**
     * Gets query for [[Responsible]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsible()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'responsible_id']);
    }
}
