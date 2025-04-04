<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "characteristic_object".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $value_type 1 - целое, 2 - дробное, 3 - строковое, 4 - булево, 5 - дата, 6 - файл, 7 - выпадающий список
 */
class CharacteristicObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'characteristic_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value_type'], 'integer'],
            [['name'], 'string', 'max' => 128],
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
            'value_type' => 'Value Type',
        ];
    }
}
