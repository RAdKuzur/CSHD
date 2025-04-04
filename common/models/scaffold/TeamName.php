<?php
namespace common\models\scaffold;
use Yii;

/**
 * This is the model class for table "team_name".
 *
 * @property int $id
 * @property string $name
 * @property int $foreign_event_id
 */
class TeamName extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team_name';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'foreign_event_id'], 'required'],
            [['foreign_event_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
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
            'foreign_event_id' => 'Foreign Event ID',
        ];
    }
}