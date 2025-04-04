<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "permission_function".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $short_code
 *
 * @property PermissionTemplateFunction[] $permissionTemplateFunctions
 * @property PermissionToken[] $permissionTokens
 * @property UserPermissionFunction[] $userPermissionFunctions
 */
class PermissionFunction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permission_function';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 128],
            [['short_code'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'short_code' => 'Short Code',
        ];
    }

    /**
     * Gets query for [[PermissionTemplateFunctions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermissionTemplateFunctions()
    {
        return $this->hasMany(PermissionTemplateFunction::class, ['function_id' => 'id']);
    }

    /**
     * Gets query for [[PermissionTokens]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermissionTokens()
    {
        return $this->hasMany(PermissionToken::class, ['function_id' => 'id']);
    }

    /**
     * Gets query for [[UserPermissionFunctions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserPermissionFunctions()
    {
        return $this->hasMany(UserPermissionFunction::class, ['function_id' => 'id']);
    }
}
