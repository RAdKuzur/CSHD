<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "patchnotes".
 *
 * @property int $id
 * @property int|null $first_number
 * @property int|null $second_number
 * @property string|null $date
 * @property string|null $text
 */
class Patchnotes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'patchnotes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_number', 'second_number'], 'integer'],
            [['date'], 'safe'],
            [['text'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_number' => 'First Number',
            'second_number' => 'Second Number',
            'date' => 'Date',
            'text' => 'Text',
        ];
    }
}
