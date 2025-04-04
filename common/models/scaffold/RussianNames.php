<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "russian_names".
 *
 * @property int $ID
 * @property string|null $Name
 * @property string|null $Sex
 * @property int|null $PeoplesCount
 * @property string|null $WhenPeoplesCount
 * @property string|null $Source
 */
class RussianNames extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'russian_names';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PeoplesCount'], 'integer'],
            [['WhenPeoplesCount'], 'safe'],
            [['Name', 'Source'], 'string', 'max' => 1024],
            [['Sex'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'Name' => 'Name',
            'Sex' => 'Sex',
            'PeoplesCount' => 'Peoples Count',
            'WhenPeoplesCount' => 'When Peoples Count',
            'Source' => 'Source',
        ];
    }
}
