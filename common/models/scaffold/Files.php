<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string|null $table_name
 * @property int|null $table_row_id
 * @property string|null $file_type
 * @property string|null $filepath
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['table_row_id'], 'integer'],
            [['table_name'], 'string', 'max' => 64],
            [['file_type'], 'string', 'max' => 32],
            [['filepath'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => 'Table Name',
            'table_row_id' => 'Table Row ID',
            'file_type' => 'File Type',
            'filepath' => 'Filepath',
        ];
    }
}
