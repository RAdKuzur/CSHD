<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "permission_template_function".
 *
 * @property int $id
 * @property int|null $template_id
 * @property int|null $function_id
 *
 * @property PermissionFunction $function
 * @property PermissionTemplate $template
 */
class PermissionTemplateFunction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permission_template_function';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['template_id', 'function_id'], 'integer'],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => PermissionTemplate::class, 'targetAttribute' => ['template_id' => 'id']],
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
            'template_id' => 'Template ID',
            'function_id' => 'Function ID',
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
     * Gets query for [[Template]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(PermissionTemplate::class, ['id' => 'template_id']);
    }
}
