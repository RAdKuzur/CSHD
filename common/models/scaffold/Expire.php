<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "expire".
 *
 * @property int $id
 * @property int|null $active_regulation_id
 * @property int|null $expire_regulation_id
 * @property int|null $expire_order_id
 * @property int|null $document_type 1 - Приказ; 2 - Исходящий; 3 - Входящий; 4 - Положение; 5 - Положение о мероприятии
 * @property int|null $expire_type 1 - Утратило силу; 2 - Изменено
 *
 * @property Regulation $activeRegulation
 * @property Regulation $expireRegulation
 */
class Expire extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'expire';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active_regulation_id', 'expire_regulation_id', 'expire_order_id', 'document_type', 'expire_type'], 'integer'],
            [['active_regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::class, 'targetAttribute' => ['active_regulation_id' => 'id']],
            [['expire_regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::class, 'targetAttribute' => ['expire_regulation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active_regulation_id' => 'Active Regulation ID',
            'expire_regulation_id' => 'Expire Regulation ID',
            'expire_order_id' => 'Expire Order ID',
            'document_type' => 'Document Type',
            'expire_type' => 'Expire Type',
        ];
    }

    /**
     * Gets query for [[ActiveRegulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActiveRegulation()
    {
        return $this->hasOne(Regulation::class, ['id' => 'active_regulation_id']);
    }

    /**
     * Gets query for [[ExpireRegulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpireRegulation()
    {
        return $this->hasOne(Regulation::class, ['id' => 'expire_regulation_id']);
    }
}
