<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "permission_template".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property PermissionTemplateFunction[] $permissionTemplateFunctions
 */
class PermissionTemplate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permission_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 64],
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
        ];
    }

    /**
     * Gets query for [[PermissionTemplateFunctions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermissionTemplateFunctions()
    {
        return $this->hasMany(PermissionTemplateFunction::class, ['template_id' => 'id']);
    }
}
