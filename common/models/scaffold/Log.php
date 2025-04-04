<?php

namespace common\models\scaffold;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property string|null $datetime
 * @property int|null $level
 * @property int|null $type
 * @property int|null $user_id
 * @property string|null $text
 * @property string|null $add_data
 *
 * @property User $user
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime'], 'safe'],
            [['level', 'type', 'user_id'], 'integer'],
            [['text', 'add_data'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'datetime' => 'Datetime',
            'level' => 'Level',
            'type' => 'Type',
            'user_id' => 'User ID',
            'text' => 'Text',
            'add_data' => 'Add Data',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
