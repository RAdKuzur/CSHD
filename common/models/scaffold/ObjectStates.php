<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "object_states".
 *
 * @property int $id
 * @property string|null $table_name
 * @property int|null $table_row_id
 * @property int|null $state 0 - доступен, 1 - открыт на чтение, 2 - открыт на запись
 * @property string|null $last_lock_time
 */
class ObjectStates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'object_states';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['table_row_id', 'state'], 'integer'],
            [['last_lock_time'], 'safe'],
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
            'table_name' => 'Table Name',
            'table_row_id' => 'Table Row ID',
            'state' => 'State',
            'last_lock_time' => 'Last Lock Time',
        ];
    }
}
