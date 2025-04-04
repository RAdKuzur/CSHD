<?php

namespace frontend\models\work\team;

use common\components\traits\ErrorTrait;
use common\models\scaffold\ActParticipantBranch;
use common\models\scaffold\SquadParticipant;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\team\TeamNameWork;
use frontend\models\work\event\ForeignEventWork;
use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\models\scaffold\ActParticipant;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
use Yii;
use yii\helpers\Url;

/**
 * @property PeopleStampWork $teacherWork
 * @property PeopleStampWork $teacher2Work
 * @property TeamNameWork $teamNameWork
 * @property ForeignEventWork $foreignEventWork
 *
 * @property ActParticipantBranchWork[] $actParticipantBranchWork
 * @property ParticipantAchievementWork[] $participantAchievementWork
 * @property SquadParticipantWork[] $squadParticipantsWork
 */
class ActParticipantWork extends ActParticipant
{
    use EventTrait, ErrorTrait;

    public $actFiles;
    public $branch;

    const TYPE_TEAM = 1;
    const TYPE_SOLO = 0;

    public static function fill(
        $teacherId,
        $teacher2Id,
        $teamNameId,
        $foreignEventId,
        $focus,
        $type,
        $allowRemote,
        $nomination,
        $form
    ){
        $entity = new static();
        $entity->teacher_id = $teacherId;
        $entity->teacher2_id = $teacher2Id;
        $entity->team_name_id = $teamNameId;
        $entity->focus = $focus;
        $entity->type = $type;
        $entity->nomination = $nomination;
        $entity->foreign_event_id = $foreignEventId;
        $entity->allow_remote = $allowRemote;
        $entity->form = $form;
        return $entity;
    }
    public function fillUpdate(
        $teacherId,
        $teacher2Id,
        $teamNameId,
        $foreignEventId,
        $focus,
        $type,
        $allowRemote,
        $nomination,
        $form
    )
    {
        $this->teacher_id = $teacherId;
        $this->teacher2_id = $teacher2Id;
        $this->team_name_id = $teamNameId;
        $this->focus = $focus;
        $this->type = $type;
        $this->nomination = $nomination;
        $this->foreign_event_id = $foreignEventId;
        $this->allow_remote = $allowRemote;
        $this->form = $form;
        return $this;
    }

    public function getTeachers()
    {
        $firstTeacher = PeopleStampWork::findOne($this->teacher_id);
        $secondTeacher = PeopleStampWork::findOne($this->teacher2_id);
        return ($firstTeacher ? $firstTeacher->getFullFio() : '') . "\n" .
            ($secondTeacher ? $secondTeacher->getFullFio() : '');
    }

    public function getTeamName()
    {
        if ($this->team_name_id && $this->type == 1) {
            $team = TeamNameWork::findOne($this->team_name_id);
            return $team->name;
        }
        else {
            return "Участие в командах не предусмотрено";
        }
    }

    public function getParticipants(){
        $participants = [];
        $squadParticipants = SquadParticipantWork::findAll(['act_participant_id' => $this->id]);
        foreach($squadParticipants as $squadParticipant){
            $person = PeopleWork::findOne($squadParticipant["participant_id"]);
            $participants[] = $person['surname'] . ' ' . $person['firstname'] . ' ' . $person['patronymic']. "\n";

        }
        return $participants;
    }
    public function getParticipantString(){
        $participants = '';
        $squadParticipants = SquadParticipantWork::findAll(['act_participant_id' => $this->id]);
        foreach($squadParticipants as $squadParticipant){
            $persons = ForeignEventParticipantsWork::findAll($squadParticipant["participant_id"]);
            foreach($persons as $person){
                $participants = $participants . $person->firstname . ' ' . $person->surname . ' ' . $person->patronymic ."\n" ;
            }
        }
        return $participants;
    }
    public function getFormattedLinkedParticipants()
    {
        $squadParticipants = SquadParticipantWork::findAll(['act_participant_id' => $this->id]);

        $participants = [];
        foreach ($squadParticipants as $participant) {
            $participants[] = StringFormatter::stringAsLink(
                $participant->participantWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS),
                Url::to(['/dictionaries/foreign-event-participants/view', 'id' => $participant->participant_id])
            );
        }

        $result = implode(', ', $participants);
        if ($this->team_name_id) {
            $result = $this->getTeamName() . ' (' . $result . ')';
        }

        return $result;
    }

    public function getTypeParticipant(){
        if($this->type == 1){
            return "Командный";
        }
        else {
            return "Личный";
        }
    }

    public function getFocusName(){
        return Yii::$app->focus->get($this->focus);
    }

    public function getBranchName(){
        $actsBranch = ActParticipantBranchWork::find()->where(['act_participant_id' => $this->id])->all();
        $fullBranch = '';
        foreach($actsBranch as $actBranch){
            $fullBranch = $fullBranch . ' ' . Yii::$app->branches->get($actBranch->branch);
        }
        return $fullBranch;
    }

    public function getFormName(){
        return Yii::$app->allowRemote->get($this->form);
    }

    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_MATERIAL:
                $addPath = FilesHelper::createAdditionalPath($this::tableName(), FilesHelper::TYPE_MATERIAL);
                break;
        }
        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    public function getString()
    {
        return 'Направленность: ' . Yii::$app->focus->get($this->focus) . '; Номинация: ' .
            (!is_null($this->nomination) ? $this->nomination : 'нет') . '; ' .
            ($this->team_name_id ? 'Командное участие' : 'Индивидуальное участие');
    }

    public function getTeachersLink()
    {
        $result = StringFormatter::stringAsLink(
            $this->teacherWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS),
            Url::to(['/dictionaries/people/view', 'id' => $this->teacherWork->people_id])
        );

        if (!is_null($this->teacher2_id)) {
            $result .= ';' . StringFormatter::stringAsLink(
                $this->teacher2Work->getFIO(PeopleWork::FIO_SURNAME_INITIALS),
                Url::to(['/dictionaries/people/view', 'id' => $this->teacher2Work->people_id])
            );
        }

        return $result;
    }

    public function getActString()
    {
        $name = $this->team_name_id ? "Команда: {$this->teamNameWork->name}" : $this->squadParticipantsWork[0]->participantWork->getFullFio();
        return $name. ' (' . $this->getString(). ')';
    }

    public function getSquadName()
    {
        return 'Редактировать: ' . $this->team_name_id ?
            'Команда ' . $this->teamNameWork->name :
            $this->getParticipants()[0]->participantWork->getFIO(PersonInterface::FIO_FULL);
    }

    public function getBranches()
    {
        return 'stub';
    }

    public function getSquadParticipantsWork()
    {
        return $this->hasMany(SquadParticipantWork::class, ['act_participant_id' => 'id']);
    }

    public function getTeacherWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'teacher_id']);
    }

    public function getTeamNameWork()
    {
        return $this->hasOne(TeamNameWork::class, ['id' => 'team_name_id']);
    }

    public function getTeacher2Work()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'teacher2_id']);
    }

    public function getForeignEventWork()
    {
        return $this->hasOne(ForeignEventWork::class, ['id' => 'foreign_event_id']);
    }

    public function getActParticipantBranchWork()
    {
        return $this->hasMany(ActParticipantBranchWork::class, ['act_participant_id' => 'id']);
    }

    public function getParticipantAchievementWork()
    {
        return $this->hasMany(ParticipantAchievementWork::class, ['act_participant_id' => 'id']);
    }

    public function getSquadParticipantWork()
    {
        return $this->hasMany(SquadParticipantWork::class, ['act_participant_id' => 'id']);
    }
}