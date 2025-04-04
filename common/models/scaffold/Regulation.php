<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "regulation".
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $name
 * @property string|null $short_name
 * @property int|null $order_id
 * @property string|null $ped_council_date
 * @property int|null $ped_council_number
 * @property string|null $par_council_date
 * @property int|null $state 0 - утратило силу; 1 - актуально
 * @property int|null $regulation_type 1 - Положения, инструкции, правила; 2 - Положения о мероприятиях
 * @property string|null $scan
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $creator
 * @property User $lastEdit
 */
class Regulation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regulation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'ped_council_date', 'par_council_date', 'created_at', 'updated_at'], 'safe'],
            [['order_id', 'ped_council_number', 'state', 'regulation_type', 'creator_id', 'last_edit_id'], 'integer'],
            [['name', 'scan'], 'string', 'max' => 512],
            [['short_name'], 'string', 'max' => 256],
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
            'date' => 'Date',
            'name' => 'Name',
            'short_name' => 'Short Name',
            'order_id' => 'Order ID',
            'ped_council_date' => 'Ped Council Date',
            'ped_council_number' => 'Ped Council Number',
            'par_council_date' => 'Par Council Date',
            'state' => 'State',
            'regulation_type' => 'Regulation Type',
            'scan' => 'Scan',
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[LastEdit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLastEdit()
    {
        return $this->hasOne(User::class, ['id' => 'last_edit_id']);
    }

    public function getDocumentOrder()
    {
        return $this->hasOne(DocumentOrder::class, ['id' => 'order_id']);
    }
}
