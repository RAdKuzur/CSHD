<?php
namespace common\models\scaffold;
use Yii;

/**
 * This is the model class for table "act_participant".
 *
 * @property int $id
 * @property int|null $teacher_id
 * @property int|null $teacher2_id
 * @property int $focus
 * @property int $type
 * @property string $nomination
 * @property int|null $team_name_id
 * @property int|null $form
 * @property int $foreign_event_id
 * @property int|null $allow_remote
 *
 * @property ForeignEvent $foreignEvent
 * @property PeopleStamp $teacher
 * @property PeopleStamp $teacher2
 * @property TeamName $teamName
 * @property ActParticipantBranch[] $actParticipantBranch
 * @property ParticipantAchievement[] $participantAchievement
 */
class ActParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'act_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_id', 'teacher2_id', 'focus', 'type', 'team_name_id', 'form', 'foreign_event_id', 'allow_remote'], 'integer'],
            //[['focus', 'type', 'nomination', 'foreign_event_id'], 'required'],
            [['nomination'], 'string', 'max' => 1000],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['teacher_id' => 'id']],
            [['teacher2_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['teacher2_id' => 'id']],
            [['team_name_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeamName::class, 'targetAttribute' => ['team_name_id' => 'id']],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::class, 'targetAttribute' => ['foreign_event_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_id' => 'Teacher ID',
            'teacher2_id' => 'Teacher2 ID',
            'focus' => 'Focus',
            'type' => 'Type',
            'nomination' => 'Nomination',
            'team_name_id' => 'Team Name ID',
            'form' => 'Form',
            'foreign_event_id' => 'Foreign Event ID',
            'allow_remote' => 'Allow Remote',
        ];
    }

    /**
     * Gets query for [[ForeignEvent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEvent()
    {
        return $this->hasOne(ForeignEvent::class, ['id' => 'foreign_event_id']);
    }

    /**
     * Gets query for [[Teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'teacher_id']);
    }

    /**
     * Gets query for [[Teacher2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher2()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'teacher2_id']);
    }

    /**
     * Gets query for [[TeamName]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeamName()
    {
        return $this->hasOne(TeamName::class, ['id' => 'team_name_id']);
    }

    public function getActParticipantBranch()
    {
        return $this->hasMany(ActParticipantBranch::class, ['act_participant_id' => 'id']);
    }

    public function getSquadParticipant()
    {
        return $this->hasMany(SquadParticipant::class, ['act_participant_id' => 'id']);
    }

    public function getParticipantAchievement()
    {
        return $this->hasMany(ParticipantAchievement::class, ['act_participant_id' => 'id']);
    }
}