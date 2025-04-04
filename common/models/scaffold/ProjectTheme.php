<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "project_theme".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $project_type
 * @property string|null $description
 */
class ProjectTheme extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_theme';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_type'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 256],
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
            'description' => 'Description',
        ];
    }
}
