<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "legacy_responsible".
 *
 * @property int $id
 * @property int|null $people_stamp_id
 * @property int|null $responsibility_type
 * @property int|null $branch
 * @property int|null $auditorium_id
 * @property int|null $quant
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $order_id
 *
 * @property Auditorium $auditorium
 * @property DocumentOrder $order
 * @property People $people
 */
class LegacyResponsible extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legacy_responsible';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['people_stamp_id', 'responsibility_type', 'branch', 'auditorium_id', 'quant', 'order_id'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::class, 'targetAttribute' => ['auditorium_id' => 'id']],
            [['people_stamp_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['people_stamp_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'people_stamp_id' => 'People ID',
            'responsibility_type' => 'Responsibility Type',
            'branch' => 'Branch',
            'auditorium_id' => 'Auditorium ID',
            'quant' => 'Quant',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'order_id' => 'Order ID',
        ];
    }

    /**
     * Gets query for [[Auditorium]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorium()
    {
        return $this->hasOne(Auditorium::class, ['id' => 'auditorium_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(DocumentOrder::class, ['id' => 'order_id']);
    }

    /**
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleStamp()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'people_stamp_id']);
    }
}
