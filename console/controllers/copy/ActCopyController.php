<?php

namespace console\controllers\copy;

use common\components\access\LogRecordComponent;
use common\repositories\act_participant\ActParticipantRepository;
use common\services\general\PeopleStampService;
use frontend\models\work\team\ActParticipantWork;
use Yii;
use yii\console\Application;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class ActCopyController extends Controller
{
    private const BLOCK_TIME = 360;
    private ActParticipantRepository $actParticipantRepository;
    private PeopleStampService $peopleStampService;
    private ParticipantAchievementCopyController $participantAchievementCopyController;
    public function __construct(
        $id,
        $module,
        ActParticipantRepository $actParticipantRepository,
        PeopleStampService $peopleStampService,
        ParticipantAchievementCopyController $participantAchievementCopyController,
        $config = [])
    {
        $this->actParticipantRepository = $actParticipantRepository;
        $this->peopleStampService = $peopleStampService;
        $this->participantAchievementCopyController = $participantAchievementCopyController;
        parent::__construct($id, $module, $config);
    }

    public function actionTeamNameCopy(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM team_name");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('team_name', $record);
            $command->execute();
        }
    }
    public function actionPersonalActCopy()
    {
        //act_participant
        $participants = Yii::$app->old_db->createCommand("SELECT * FROM teacher_participant")->queryAll();
        foreach ($participants as $participant) {
            $participantId = $participant['id'];
            if (count(Yii::$app->old_db->createCommand("SELECT * FROM team WHERE teacher_participant_id = $participantId")->queryAll()) == 0) {
                $actModel = ActParticipantWork::fill(
                    $participant['teacher_id'] != '' ? $this->peopleStampService->createStampFromPeople($participant['teacher_id']) : NULL,
                    $participant['teacher2_id'] != '' ? $this->peopleStampService->createStampFromPeople($participant['teacher2_id']) : NULL,
                    NULL,
                    $participant['foreign_event_id'],
                    $participant['focus'],
                    NULL,
                    $participant['allow_remote_id'],
                    $participant['nomination'],
                    $participant['allow_remote_id']
                );
                $this->actParticipantRepository->save($actModel);
                //squad_participant
                $command = Yii::$app->db->createCommand();
                $command->insert('squad_participant',
                    [
                        'act_participant_id' => $actModel->id,
                        'participant_id' => $participant['participant_id'],
                    ]
                );
                $command->execute();
                //act_participant_branch
                $branches = Yii::$app->old_db->createCommand("SELECT * FROM teacher_participant_branch WHERE teacher_participant_id = $participantId")->queryAll();
                foreach ($branches as $branch) {
                    $command = Yii::$app->db->createCommand();
                    $command->insert('act_participant_branch',
                        [
                            'act_participant_id' => $actModel->id,
                            'branch' => $branch['branch_id'],
                        ]
                    );
                    $command->execute();
                }
            }
            //participant_achievement
            $this->participantAchievementCopyController->actionCopyParticipantAchievement($participantId, $actModel->id);
        }
    }
    public function actionTeamActCopy()
    {
        $groupedArray = [];
        $inputArray = Yii::$app->old_db->createCommand("SELECT * FROM team WHERE team_name_id IS NOT NULL")->queryAll();
        foreach ($inputArray as $item) {
            $teamNameId = $item['team_name_id'];
            if (!isset($groupedArray[$teamNameId])) {
                $groupedArray[$teamNameId] = [];
            }
            $groupedArray[$teamNameId][] = $item;
        }
        $groupedArray = array_values($groupedArray);
        //act_participant
        foreach ($groupedArray as $act) {
            $participantId = $act[0]['teacher_participant_id'];
            $participant = Yii::$app->old_db->createCommand("SELECT * FROM teacher_participant WHERE id = $participantId")->queryOne();
            $actModel = ActParticipantWork::fill(
                $participant['teacher_id'] != '' ? $this->peopleStampService->createStampFromPeople($participant['teacher_id']) : NULL,
                $participant['teacher2_id'] != '' ? $this->peopleStampService->createStampFromPeople($participant['teacher2_id']) : NULL,
                $act[0]['team_name_id'],
                $participant['foreign_event_id'],
                $participant['focus'],
                1,
                $participant['allow_remote_id'],
                $participant['nomination'],
                $participant['allow_remote_id']
            );
            $this->actParticipantRepository->save($actModel);
            //participant_achievement
            foreach (Yii::$app->old_db->createCommand("SELECT * FROM teacher_participant WHERE id = $participantId")->queryAll() as $item) {
                //$this->participantAchievementCopyController->actionCopyParticipantAchievement($item['id'], $actModel->id);
            }
            //squad_participant
            foreach($act as $participant){
                $participantId = $participant['teacher_participant_id'];
                $command = Yii::$app->db->createCommand();
                $command->insert('squad_participant',
                    [
                        'act_participant_id' => $actModel->id,
                        'participant_id' => (Yii::$app->old_db->createCommand("SELECT * FROM teacher_participant WHERE id = $participantId")->queryOne())['participant_id']
                    ]
                );
                $command->execute();
            }
            //act_participant_branch
            $participants = ArrayHelper::getColumn($act, 'teacher_participant_id');
            $participantIds = implode(',', $participants);
            $branches = array_unique(Yii::$app->old_db->createCommand("SELECT branch_id FROM teacher_participant_branch WHERE teacher_participant_id IN ($participantIds)")->queryColumn());
            foreach ($branches as $branch) {
                $command = Yii::$app->db->createCommand();
                $command->insert('act_participant_branch',
                    [
                        'act_participant_id' => $actModel->id,
                        'branch' => $branch,
                    ]
                );
            }
            $command->execute();
        }
    }
    public function actionActCopy()
    {
        $this->actionTeamActCopy();
        $this->actionPersonalActCopy();
    }
    public function actionCopyAll(){
        Yii::$app->logRecord->block('BLOCK_LOG', self::BLOCK_TIME);
        $this->actionTeamNameCopy();
        $this->actionActCopy();
        Yii::$app->logRecord->unblock('BLOCK_LOG');
    }
    public function actionDeleteTeamName()
    {
        Yii::$app->db->createCommand()->delete('team_name')->execute();
    }
    public function actionDeleteActParticipant()
    {
        Yii::$app->db->createCommand()->delete('act_participant')->execute();
    }
    public function actionDeleteSquadParticipant()
    {
        Yii::$app->db->createCommand()->delete('squad_participant')->execute();
    }
    public function actionDeleteActParticipantBranch()
    {
        Yii::$app->db->createCommand()->delete('act_participant_branch')->execute();
    }
    public function actionDeleteAll()
    {
        $this->participantAchievementCopyController->actionDeleteAll();
        $this->actionDeleteActParticipantBranch();
        $this->actionDeleteSquadParticipant();
        $this->actionDeleteActParticipant();
        $this->actionDeleteTeamName();
    }
}