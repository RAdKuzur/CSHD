<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "user_permission_function".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $function_id
 * @property int|null $branch
 *
 * @property PermissionFunction $function
 * @property User $user
 */
class UserPermissionFunction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_permission_function';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'function_id', 'branch'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['function_id'], 'exist', 'skipOnError' => true, 'targetClass' => PermissionFunction::class, 'targetAttribute' => ['function_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'function_id' => 'Function ID',
            'branch' => 'Branch',
        ];
    }

    /**
     * Gets query for [[Function]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFunction()
    {
        return $this->hasOne(PermissionFunction::class, ['id' => 'function_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
