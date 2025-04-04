<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "foreign_event".
 *
 * @property int $id
 * @property int $order_participant_id
 * @property string $name
 * @property int|null $organizer_id
 * @property string $begin_date
 * @property string $end_date
 * @property string|null $city
 * @property int|null $format
 * @property int|null $level
 * @property int|null $minister
 * @property int|null $min_age
 * @property int|null $max_age
 * @property string|null $key_words
 * @property int|null $escort_id
 * @property int|null $add_order_participant_id
 * @property int|null $order_business_trip_id
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 *
 * @property DocumentOrder $addOrderParticipant
 * @property PeopleStamp $escort
 * @property DocumentOrder $orderBusinessTrip
 * @property DocumentOrder $orderParticipant
 * @property Company $organizer
 * @property User $creator
 * @property User $lastEdit
 */
class ForeignEvent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'foreign_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_participant_id', 'name', 'begin_date', 'end_date'], 'required'],
            [['order_participant_id', 'organizer_id', 'format', 'level', 'minister', 'min_age', 'max_age', 'escort_id', 'add_order_participant_id', 'order_business_trip_id'], 'integer'],
            [['begin_date', 'end_date'], 'safe'],
            [['name', 'city', 'key_words'], 'string', 'max' => 128],
            [['order_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::class, 'targetAttribute' => ['order_participant_id' => 'id']],
            [['organizer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['organizer_id' => 'id']],
            [['escort_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['escort_id' => 'id']],
            [['add_order_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::class, 'targetAttribute' => ['add_order_participant_id' => 'id']],
            [['order_business_trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::class, 'targetAttribute' => ['order_business_trip_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['last_edit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_participant_id' => 'Order Participant ID',
            'name' => 'Название',
            'organizer_id' => 'Организатор',
            'begin_date' => 'Дата начала',
            'end_date' => 'Дата окончания',
            'city' => 'Город',
            'format' => 'Формат проведения',
            'level' => 'Уровень',
            'minister' => 'Minister',
            'min_age' => 'Min Age',
            'max_age' => 'Max Age',
            'key_words' => 'Key Words',
            'escort_id' => 'Escort ID',
            'add_order_participant_id' => 'Add Order Participant ID',
            'order_business_trip_id' => 'Order Business Trip ID',
        ];
    }

    /**
     * Gets query for [[AddOrderParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddOrderParticipant()
    {
        return $this->hasOne(DocumentOrder::class, ['id' => 'add_order_participant_id']);
    }

    /**
     * Gets query for [[Escort]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEscort()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'escort_id']);
    }

    /**
     * Gets query for [[OrderBusinessTrip]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderBusinessTrip()
    {
        return $this->hasOne(DocumentOrder::class, ['id' => 'order_business_trip_id']);
    }

    /**
     * Gets query for [[OrderParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderParticipant()
    {
        return $this->hasOne(DocumentOrder::class, ['id' => 'order_participant_id']);
    }

    /**
     * Gets query for [[Organizer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizer()
    {
        return $this->hasOne(Company::class, ['id' => 'organizer_id']);
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    public function getLastEdit()
    {
        return $this->hasOne(User::class, ['id' => 'last_edit_id']);
    }
}
