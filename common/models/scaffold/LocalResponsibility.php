<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "local_responsibility".
 *
 * @property int $id
 * @property int|null $responsibility_type
 * @property int|null $branch
 * @property int|null $auditorium_id
 * @property int|null $quant
 * @property int|null $people_stamp_id
 * @property int|null $regulation_id
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 *
 * @property Auditorium $auditorium
 * @property PeopleStamp $peopleStamp
 * @property Regulation $regulation
 */
class LocalResponsibility extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'local_responsibility';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['responsibility_type', 'branch', 'auditorium_id', 'quant', 'people_stamp_id', 'regulation_id', 'creator_id', 'last_edit_id'], 'integer'],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['last_edit_id' => 'id']],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::class, 'targetAttribute' => ['auditorium_id' => 'id']],
            [['people_stamp_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['people_stamp_id' => 'id']],
            [['regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::class, 'targetAttribute' => ['regulation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'responsibility_type' => 'Responsibility Type',
            'branch' => 'Branch',
            'auditorium_id' => 'Auditorium ID',
            'quant' => 'Quant',
            'people_stamp_id' => 'People ID',
            'regulation_id' => 'Regulation ID',
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
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
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleStamp()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'people_stamp_id']);
    }

    /**
     * Gets query for [[Regulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegulation()
    {
        return $this->hasOne(Regulation::class, ['id' => 'regulation_id']);
    }
}
