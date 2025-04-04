<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "errors".
 *
 * @property int $id
 * @property int|null $error
 * @property string|null $table_name
 * @property int|null $table_row_id
 * @property string|null $create_datetime
 * @property int|null $was_amnesty
 * @property int|null $branch
 * @property int|null $state
 */
class Errors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['error', 'table_name', 'table_row_id', 'create_datetime'], 'default', 'value' => null],
            [['was_amnesty'], 'default', 'value' => 0],
            [['error', 'table_row_id', 'was_amnesty', 'branch', 'state'], 'integer'],
            [['create_datetime'], 'safe'],
            [['table_name'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'error' => 'Error',
            'table_name' => 'Table Name',
            'table_row_id' => 'Table Row ID',
            'create_datetime' => 'Create Datetime',
            'was_amnesty' => 'Was Amnesty',
        ];
    }

}
