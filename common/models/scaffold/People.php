<?php

namespace common\models\scaffold;


/**
 * This is the model class for table "people".
 *
 * @property int $id
 * @property string $firstname
 * @property string $surname
 * @property string|null $patronymic
 * @property int|null $company_id
 * @property int|null $position_id
 * @property string|null $short
 * @property int|null $branch
 * @property string|null $birthdate
 * @property int|null $sex
 * @property string|null $genitive_surname
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_id
 * @property int|null $last_edit_id
 *
 * @property Company $company
 * @property DocumentIn[] $documentIns
 * @property DocumentIn[] $documentIns0
 * @property DocumentOut[] $documentOuts
 * @property DocumentOut[] $documentOuts0
 * @property DocumentOut[] $documentOuts1
 * @property Position $position
 * @property User[] $users
 */
class People extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'people';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname', 'surname'], 'required'],
            [['company_id', 'position_id', 'branch', 'sex', 'creator_id', 'last_edit_id'], 'integer'],
            [['birthdate', 'created_at', 'updated_at', 'id'], 'safe'],
            [['firstname', 'surname', 'patronymic', 'genitive_surname'], 'string', 'max' => 256],
            [['short'], 'string', 'max' => 10],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::class, 'targetAttribute' => ['position_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Имя',
            'surname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'company_id' => 'Company ID',
            'position_id' => 'Position ID',
            'short' => 'Short',
            'branch' => 'Branch',
            'birthdate' => 'Birthdate',
            'sex' => 'Sex',
            'genitive_surname' => 'Genitive surname',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * Gets query for [[DocumentIns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentIns()
    {
        return $this->hasMany(DocumentIn::class, ['correspondent_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentIns0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentIns0()
    {
        return $this->hasMany(DocumentIn::class, ['signed_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOuts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOuts()
    {
        return $this->hasMany(DocumentOut::class, ['correspondent_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOuts0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOuts0()
    {
        return $this->hasMany(DocumentOut::class, ['signed_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOuts1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOuts1()
    {
        return $this->hasMany(DocumentOut::class, ['executor_id' => 'id']);
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

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['aka' => 'id']);
    }
}
