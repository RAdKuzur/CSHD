<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "training_program".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $thematic_direction
 * @property int|null $level
 * @property string|null $ped_council_date
 * @property string|null $ped_council_number
 * @property int|null $author_id
 * @property int|null $capacity
 * @property int|null $hour_capacity
 * @property float|null $student_left_age
 * @property int|null $student_right_age
 * @property int|null $focus
 * @property int|null $allow_remote
 * @property int|null $actual
 * @property int|null $certificate_type
 * @property string|null $description
 * @property string|null $key_words
 * @property int|null $is_network
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property PeopleStamp $author
 * @property User $creator
 * @property User $lastUpdate
 */
class TrainingProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['thematic_direction', 'level', 'author_id', 'capacity', 'hour_capacity', 'student_right_age', 'focus', 'allow_remote', 'actual', 'certificate_type', 'is_network', 'creator_id', 'last_edit_id'], 'integer'],
            [['ped_council_date', 'created_at', 'updated_at'], 'safe'],
            [['student_left_age'], 'number'],
            [['name', 'description', 'key_words'], 'string', 'max' => 1024],
            [['ped_council_number'], 'string', 'max' => 128],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['author_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['last_edit_id' => 'id']],
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
            'thematic_direction' => 'Thematic Direction',
            'level' => 'Level',
            'ped_council_date' => 'Ped Council Date',
            'ped_council_number' => 'Ped Council Number',
            'author_id' => 'Author ID',
            'capacity' => 'Capacity',
            'hour_capacity' => 'Hour Capacity',
            'student_left_age' => 'Student Left Age',
            'student_right_age' => 'Student Right Age',
            'focus' => 'Focus',
            'allow_remote' => 'Allow Remote',
            'actual' => 'Actual',
            'certificate_type' => 'Certificate Type',
            'description' => 'Description',
            'key_words' => 'Key Words',
            'is_network' => 'Is Network',
            'creator_id' => 'Creator ID',
            'last_update_id' => 'Last Update ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(AuthorProgram::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasMany(BranchProgram::class, ['training_program_id' => 'id']);
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
     * Gets query for [[LastUpdate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLastUpdate()
    {
        return $this->hasOne(User::class, ['id' => 'last_update_id']);
    }
}
