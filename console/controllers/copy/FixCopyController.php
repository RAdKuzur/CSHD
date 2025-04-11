<?php

namespace console\controllers\copy;

use common\models\scaffold\TrainingGroup;
use frontend\models\work\educational\training_group\LessonThemeWork;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\ThematicPlanWork;
use frontend\models\work\event\EventWork;
use frontend\services\educational\TrainingGroupService;
use Yii;
use yii\console\Controller;
/* @var $lessonTheme LessonThemeWork*/
class FixCopyController extends Controller
{
    private TrainingGroupService $trainingGroupService;
    public function __construct(
        $id,
        $module,
        TrainingGroupService $trainingGroupService,
        $config = []
    )
    {
        $this->trainingGroupService = $trainingGroupService;
        parent::__construct($id, $module, $config);
    }

    public function actionFixThematicPlan()
    {
        ini_set('memory_limit', '-1');
        $groups = TrainingGroupWork::find()->all();
        foreach ($groups as $group) {
            $this->trainingGroupService->createLessonThemes($group->id);
        }
    }
    public function actionFixOrderTrainingGroupParticipant(){
        /* @var $orderTGP OrderTrainingGroupParticipantWork*/
        ini_set('memory_limit', '-1');
        $orderTGPs = OrderTrainingGroupParticipantWork::find()->where([
            'IS NOT', 'training_group_participant_in_id', NULL
        ])->andWhere([
            'IS NOT', 'training_group_participant_out_id', NULL
        ])->all();
        foreach ($orderTGPs as $orderTGP) {
            if ($orderTGP->order->order_date <= '2025-04-03'){
                $inId = $orderTGP->training_group_participant_in_id;
                $outId = $orderTGP->training_group_participant_out_id;
                if (!is_null($inId) && !is_null($outId)){
                    $orderTGP->training_group_participant_in_id = $outId;
                    $orderTGP->training_group_participant_out_id = $inId;
                    $orderTGP->save();
                }
            }
        }
    }
    public function actionFixEvents(){
        /* @var $event EventWork */
        ini_set('memory_limit', '-1');
        $events = EventWork::find()->all();
        foreach ($events as $event) {
            $eventId = $event->id;
            $oldEvent = Yii::$app->db->createCommand("SELECT * FROM event_participants WHERE event_id = $eventId")->queryOne();
            if($oldEvent) {
                $event->child_participants_count = $oldEvent['child_participants'];
                $event->child_rst_participants_count = $oldEvent['child_rst_participants'];
                $event->teacher_participants_count = $oldEvent['teacher_participants'];
                $event->other_participants_count = $oldEvent['other_participants'];
                $event->age_left_border = $oldEvent['age_left_border'];
                $event->age_right_border = $oldEvent['age_right_border'];
                $event->save();
            }
        }
    }
}