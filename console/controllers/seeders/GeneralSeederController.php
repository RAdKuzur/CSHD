<?php

namespace console\controllers\seeders;

use common\models\scaffold\TrainingGroup;
use yii\console\Controller;

class GeneralSeederController extends Controller
{
    private TrainingProgramSeederController $trainingProgramSeederController;
    private ThematicPlanSeederController $thematicPlanSeederController;
    private TrainingGroupSeederController $trainingGroupSeederController;
    private TrainingGroupParticipantSeederController $trainingGroupParticipantSeederController;
    private TrainingGroupLessonSeederController $trainingGroupLessonSeederController;
    private LessonThemeSeederController $lessonThemeSeederController;
    private VisitSeederController $visitSeederController;
    private ForeignEventSeederController $foreignEventSeederController;
    private ActParticipantSeederController $actParticipantSeederController;
    private ActParticipantBranchSeederController $actParticipantBranchSeederController;
    private SquadParticipantSeederController $squadParticipantSeederController;
    private ParticipantAchievementSeederController $participantAchievementSeederController;
    public function __construct(
        $id,
        $module,
        TrainingProgramSeederController $trainingProgramSeederController,
        ThematicPlanSeederController $thematicPlanSeederController,
        TrainingGroupSeederController $trainingGroupSeederController,
        TrainingGroupParticipantSeederController $trainingGroupParticipantSeederController,
        TrainingGroupLessonSeederController $trainingGroupLessonSeederController,
        LessonThemeSeederController $lessonThemeSeederController,
        VisitSeederController $visitSeederController,
        ForeignEventSeederController $foreignEventSeederController,
        ActParticipantSeederController $actParticipantSeederController,
        ActParticipantBranchSeederController $actParticipantBranchSeederController,
        SquadParticipantSeederController $squadParticipantSeederController,
        ParticipantAchievementSeederController $participantAchievementSeederController,
        $config = [])
    {
        $this->trainingProgramSeederController = $trainingProgramSeederController;
        $this->thematicPlanSeederController = $thematicPlanSeederController;
        $this->trainingGroupSeederController = $trainingGroupSeederController;
        $this->trainingGroupParticipantSeederController = $trainingGroupParticipantSeederController;
        $this->trainingGroupLessonSeederController = $trainingGroupLessonSeederController;
        $this->lessonThemeSeederController = $lessonThemeSeederController;
        $this->visitSeederController = $visitSeederController;
        $this->foreignEventSeederController = $foreignEventSeederController;
        $this->actParticipantSeederController = $actParticipantSeederController;
        $this->actParticipantBranchSeederController = $actParticipantBranchSeederController;
        $this->squadParticipantSeederController = $squadParticipantSeederController;
        $this->participantAchievementSeederController = $participantAchievementSeederController;
        parent::__construct($id, $module, $config);
    }
    public function actionCreateStudy(){
        $this->trainingProgramSeederController->actionRun(20);
        $this->thematicPlanSeederController->actionRun(15);
        $this->trainingGroupSeederController->actionRun(15);
        $this->trainingGroupParticipantSeederController->actionRun(400);
        $this->trainingGroupLessonSeederController->actionRun(3000);
        $this->lessonThemeSeederController->actionRun();
        $this->visitSeederController->actionRun();
    }
    public function actionCreateEvent(){
        $this->foreignEventSeederController->actionRun();
        $this->actParticipantSeederController->actionRun();
        $this->actParticipantBranchSeederController->actionRun();
        $this->squadParticipantSeederController->actionRun();
        $this->participantAchievementSeederController->actionRun();
    }
    public function actionDeleteStudy(){
        $this->visitSeederController->actionDelete();
        $this->lessonThemeSeederController->actionDelete();
        $this->trainingGroupLessonSeederController->actionDelete();
        $this->trainingGroupParticipantSeederController->actionDelete();
        $this->trainingGroupSeederController->actionDelete();
        $this->thematicPlanSeederController->actionDelete();
        $this->trainingProgramSeederController->actionDelete();
    }
    public function actionDeleteEvent(){
        $this->participantAchievementSeederController->actionDelete();
        $this->squadParticipantSeederController->actionDelete();
        $this->actParticipantBranchSeederController->actionDelete();
        $this->actParticipantSeederController->actionDelete();
        $this->foreignEventSeederController->actionDelete();
    }
    public function actionCreateAll(){
        $this->actionCreateStudy();
        $this->actionCreateEvent();
    }
    public function actionDeleteAll(){
        $this->actionDeleteStudy();
        $this->actionDeleteEvent();
    }
}