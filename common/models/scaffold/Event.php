<?php

namespace common\models\scaffold;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $start_date
 * @property string|null $finish_date
 * @property int|null $event_type
 * @property int|null $event_form
 * @property int|null $event_level
 * @property int|null $event_way
 * @property string|null $address
 * @property int|null $is_federal
 * @property int|null $responsible1_id
 * @property int|null $responsible2_id
 * @property string|null $key_words
 * @property string|null $comment
 * @property int|null $order_id
 * @property int|null $regulation_id
 * @property int|null $contains_education
 * @property int|null $participation_scope
 * @property int|null $child_participants_count
 * @property int|null $child_rst_participants_count
 * @property int|null $teacher_participants_count
 * @property int|null $other_participants_count
 * @property float|null $age_left_border
 * @property int|null $age_right_border
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $creator
 * @property EventBranch[] $eventBranches
 * @property Regulation $regulation
 * @property PeopleStamp $responsible1
 * @property PeopleStamp $responsible2
 * @property EventTrainingGroup[] $eventTrainingGroups
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_date', 'finish_date', 'created_at', 'updated_at'], 'safe'],
            [['event_type', 'event_form', 'event_level', 'event_way', 'is_federal', 'responsible1_id', 'responsible2_id', 'order_id', 'regulation_id', 'contains_education', 'participation_scope', 'child_participants_count', 'child_rst_participants_count', 'teacher_participants_count', 'other_participants_count', 'age_right_border', 'creator_id'], 'integer'],
            [['age_left_border'], 'number'],
            [['name'], 'string', 'max' => 512],
            [['address', 'key_words', 'comment'], 'string', 'max' => 1024],
            [['responsible1_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['responsible1_id' => 'id']],
            [['responsible2_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['responsible2_id' => 'id']],
            [['regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::class, 'targetAttribute' => ['regulation_id' => 'id']],
            [['creator_id', 'last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название мероприятия',
            'start_date' => 'Дата начала мероприятия',
            'finish_date' => 'Дата окончания мероприятия',
            'event_type' => 'Event Type',
            'event_form' => 'Event Form',
            'event_level' => 'Event Level',
            'event_way' => 'Event Way',
            'address' => 'Адрес проведения',
            'is_federal' => 'Is Federal',
            'responsible1_id' => 'Responsible1 ID',
            'responsible2_id' => 'Responsible2 ID',
            'key_words' => 'Key Words',
            'comment' => 'Comment',
            'order_id' => 'Order ID',
            'regulation_id' => 'Regulation ID',
            'contains_education' => 'Contains Education',
            'participation_scope' => 'Participation Scope',
            'child_participants_count' => 'Child Participants Count',
            'child_rst_participants_count' => 'Child Rst Participants Count',
            'teacher_participants_count' => 'Teacher Participants Count',
            'other_participants_count' => 'Other Participants Count',
            'age_left_border' => 'Age Left Border',
            'age_right_border' => 'Age Right Border',
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[EventBranches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranches()
    {
        return $this->hasMany(EventBranch::class, ['event_id' => 'id']);
    }

    /**
     * Gets query for [[Regulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegulation()
    {
        return $this->hasOne(Regulation::class, ['id' => 'regulation_id']);
    }

    /**
     * Gets query for [[Responsible1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsible1()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'responsible1_id']);
    }

    /**
     * Gets query for [[Responsible2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsible2()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'responsible2_id']);
    }

    public function getDocumentOrder()
    {
        return $this->hasOne(DocumentOrder::class, ['id' => 'order_id']);
    }

    public function getScopes()
    {
        return $this->hasMany(EventScope::class, ['event_id' => 'id']);
    }

    public function getTrainingGroups()
    {
        return $this->hasMany(EventTrainingGroup::class, ['event_id' => 'id']);
    }
}
