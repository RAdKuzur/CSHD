<?php
namespace frontend\models\forms;
class ActParticipantForm extends \yii\base\Model
{
    public $actId;
    public $participant;
    public $firstTeacher;
    public $secondTeacher;
    public $branch;
    public $focus;
    public $type;
    public $nomination;
    public $team;
    public $form;
    public $actFiles;


    public $foreignEventId;
    public $allowRemote;
    /**
     * {@inheritdoc}
     */
    /**
     * {@inheritdoc}
     */
    public $personalParticipants;
    public static function fill(
        $participant,
        $teacherId,
        $teacher2Id,
        $branch,
        $focus,
        $type,
        $allowRemote,
        $nomination,
        $form,
        $team
    ){
        $entity = new static();
        $entity->participant = $participant;
        $entity->firstTeacher = $teacherId;
        $entity->secondTeacher = $teacher2Id;
        $entity->branch = $branch;
        $entity->focus = $focus;
        $entity->type = $type;
        $entity->nomination = $nomination;
        $entity->allowRemote = $allowRemote;
        $entity->form = $form;
        $entity->team = $team;
        return $entity;
    }
    public function rules()
    {
        return [
            [['teacher_id', 'teacher2_id', 'branch', 'focus', 'type', 'team_name_id', 'foreign_event_id', 'allow_remote', 'form'], 'integer'],
            [['nomination'], 'string', 'max' => 1000],
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
            'branch' => 'Branch',
            'focus' => 'Focus',
            'type' => 'Type',
            'nomination' => 'Nomination',
            'team_name_id' => 'Team Name ID',
            'foreign_event_id' => 'Foreign Event ID',
            'allow_remote' => 'Allow Remote',
            'form' => 'Form',
        ];
    }
}