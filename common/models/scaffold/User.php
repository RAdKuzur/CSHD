<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $firstname
 * @property string $surname
 * @property string|null $patronymic
 * @property string $username
 * @property string|null $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string|null $email
 * @property int|null $aka
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 *
 * @property People $aka0
 * @property Company[] $companies
 * @property User $creator
 * @property DocumentIn[] $documentIns
 * @property DocumentIn[] $documentIns0
 * @property DocumentIn[] $documentIns1
 * @property DocumentOut[] $documentOuts
 * @property DocumentOut[] $documentOuts0
 * @property User $lastEdit
 * @property PermissionToken[] $permissionTokens
 * @property UserPermissionFunction[] $userPermissionFunctions
 * @property User[] $users
 * @property User[] $users0
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname', 'surname', 'username', 'password_hash'], 'required'],
            [['aka', 'status', 'creator_id', 'last_edit_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['firstname', 'surname', 'patronymic', 'username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 256],
            [['auth_key'], 'string', 'max' => 32],
            [['aka'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['aka' => 'id']],
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
            'firstname' => 'Firstname',
            'surname' => 'Surname',
            'patronymic' => 'Patronymic',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'aka' => 'Aka',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
        ];
    }

    /**
     * Gets query for [[Aka0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAka0()
    {
        return $this->hasOne(People::class, ['id' => 'aka']);
    }

    /**
     * Gets query for [[Companies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::class, ['last_edit_id' => 'id']);
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
     * Gets query for [[DocumentIns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentIns()
    {
        return $this->hasMany(DocumentIn::class, ['get_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentIns0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentIns0()
    {
        return $this->hasMany(DocumentIn::class, ['creator_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentIns1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentIns1()
    {
        return $this->hasMany(DocumentIn::class, ['last_edit_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOuts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOuts()
    {
        return $this->hasMany(DocumentOut::class, ['creator_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOuts0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOuts0()
    {
        return $this->hasMany(DocumentOut::class, ['last_edit_id' => 'id']);
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
     * Gets query for [[PermissionTokens]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermissionTokens()
    {
        return $this->hasMany(PermissionToken::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UserPermissionFunctions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserPermissionFunctions()
    {
        return $this->hasMany(UserPermissionFunction::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['creator_id' => 'id']);
    }

    /**
     * Gets query for [[Users0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany(User::class, ['last_edit_id' => 'id']);
    }
}
