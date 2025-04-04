<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property int|null $company_type 1 - образовательное учреждение, 2 - государственное учреждение, 3 - частная организация/ИП
 * @property string $name
 * @property string $short_name
 * @property int $is_contractor
 * @property string|null $inn
 * @property int|null $category_smsp 1 - микропредприятие, 2 - малое предприятие, 3 - среднее предприятие, 4 - самозанятый, 5 - НЕ СМСП
 * @property string|null $comment
 * @property int|null $last_edit_id
 * @property string|null $phone_number
 * @property string|null $email
 * @property string|null $site
 * @property int|null $ownership_type 1 - бюджетное, 2 - автономное, 3 - казённое, 4 - унитарное, 5 - НКО, 6 - нетиповое, 7 - ООО, 8 - ИП, 9 - ПАО, 10 - АО, 11 - ЗАО, 12 - физлицо, 13 - прочее
 * @property string|null $okved
 * @property string|null $head_fio
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property DocumentIn[] $documentIns
 * @property DocumentOut[] $documentOuts
 * @property User $lastEdit
 * @property People[] $peoples
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_type', 'is_contractor', 'category_smsp', 'last_edit_id', 'ownership_type'], 'integer'],
            [['name', 'short_name', 'is_contractor'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'short_name'], 'string', 'max' => 128],
            [['inn'], 'string', 'max' => 15],
            [['comment', 'email', 'site', 'head_fio'], 'string', 'max' => 256],
            [['phone_number', 'okved'], 'string', 'max' => 12],
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
            'company_type' => 'Company Type',
            'name' => 'Название организации',
            'short_name' => 'Краткое название организации',
            'is_contractor' => 'Является контрагентом',
            'inn' => 'Inn',
            'category_smsp' => 'Category Smsp',
            'comment' => 'Comment',
            'last_edit_id' => 'Last Edit ID',
            'phone_number' => 'Phone Number',
            'email' => 'Email',
            'site' => 'Site',
            'ownership_type' => 'Ownership Type',
            'okved' => 'Okved',
            'head_fio' => 'Head Fio',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[DocumentIns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentIns()
    {
        return $this->hasMany(DocumentIn::class, ['company_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOuts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOuts()
    {
        return $this->hasMany(DocumentOut::class, ['company_id' => 'id']);
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

    /**
     * Gets query for [[Peoples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeoples()
    {
        return $this->hasMany(People::class, ['company_id' => 'id']);
    }
}
