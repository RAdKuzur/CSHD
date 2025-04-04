<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "people_stamp".
 *
 * @property int $id
 * @property int $people_id
 * @property string|null $surname
 * @property string|null $genitive_surname
 * @property int|null $position_id
 * @property int|null $company_id
 *
 * @property Company $company
 * @property People $people
 * @property Position $position
 */
class PeopleStamp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'people_stamp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['people_id'], 'required'],
            [['people_id', 'position_id', 'company_id'], 'integer'],
            [['surname', 'genitive_surname'], 'string', 'max' => 256],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['people_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::class, 'targetAttribute' => ['position_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'people_id' => 'People ID',
            'surname' => 'Surname',
            'genitive_surname' => 'Genitive Surname',
            'position_id' => 'Position ID',
            'company_id' => 'Company ID',
        ];
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasOne(People::class, ['id' => 'people_id']);
    }

    /**
     * Gets query for [[Position]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(Position::class, ['id' => 'position_id']);
    }
}
