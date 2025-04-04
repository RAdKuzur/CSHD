<?php

namespace console\controllers\copy;

use yii\console\Controller;

class GeneralCopyController extends Controller
{
    private InitCopyController $initCopyController;
    private DocumentInOutCopyController $documentInOutCopyController;
    private PeopleTablesCopyController $peopleTablesCopyController;
    private PersonalDataCopyController $personalDataCopyController;
    private RegulationCopyController $regulationCopyController;
    private EventCopyController $eventCopyController;
    private TrainingProgramCopyController $trainingProgramCopyController;
    private DocumentOrderCopyController $documentOrderCopyController;
    private LocalResponsibilityCopyController $localResponsibilityCopyController;
    private ForeignEventCopyController $foreignEventCopyController;
    private ActCopyController $actCopyController;
    private TrainingGroupCopyController $trainingGroupCopyController;
    private VisitCopyController $visitCopyController;
    private LessonThemeCopyController $lessonThemeCopyController;
    private ParticipantAchievementCopyController $participantAchievementCopyController;
    private CertificateCopyController $certificateCopyController;
    private LogCopyController $logCopyController;
    private EventTrainingGroupCopyController $eventTrainingGroupCopyController;
    private OrderPeopleCopyController $orderPeopleCopyController;
    private FilesCopyController $filesCopyController;
    private ExpireCopyController $expireCopyController;
    public function __construct(
        $id,
        $module,
        InitCopyController $initCopyController,
        DocumentInOutCopyController $documentInOutCopyController,
        PeopleTablesCopyController $peopleTablesCopyController,
        PersonalDataCopyController $personalDataCopyController,
        RegulationCopyController $regulationCopyController,
        EventCopyController $eventCopyController,
        TrainingProgramCopyController $trainingProgramCopyController,
        DocumentOrderCopyController $documentOrderCopyController,
        LocalResponsibilityCopyController $localResponsibilityCopyController,
        ForeignEventCopyController $foreignEventCopyController,
        ActCopyController $actCopyController,
        TrainingGroupCopyController $trainingGroupCopyController,
        VisitCopyController $visitCopyController,
        LessonThemeCopyController $lessonThemeCopyController,
        ParticipantAchievementCopyController $participantAchievementCopyController,
        CertificateCopyController $certificateCopyController,
        LogCopyController $logCopyController,
        EventTrainingGroupCopyController $eventTrainingGroupCopyController,
        OrderPeopleCopyController $orderPeopleCopyController,
        FilesCopyController $filesCopyController,
        ExpireCopyController $expireCopyController,
        $config = [])
    {
        $this->initCopyController = $initCopyController;
        $this->documentInOutCopyController = $documentInOutCopyController;
        $this->peopleTablesCopyController = $peopleTablesCopyController;
        $this->personalDataCopyController = $personalDataCopyController;
        $this->regulationCopyController = $regulationCopyController;
        $this->eventCopyController = $eventCopyController;
        $this->trainingProgramCopyController = $trainingProgramCopyController;
        $this->documentOrderCopyController = $documentOrderCopyController;
        $this->localResponsibilityCopyController = $localResponsibilityCopyController;
        $this->foreignEventCopyController = $foreignEventCopyController;
        $this->actCopyController = $actCopyController;
        $this->trainingGroupCopyController = $trainingGroupCopyController;
        $this->visitCopyController = $visitCopyController;
        $this->lessonThemeCopyController = $lessonThemeCopyController;
        $this->participantAchievementCopyController = $participantAchievementCopyController;
        $this->certificateCopyController = $certificateCopyController;
        $this->logCopyController = $logCopyController;
        $this->eventTrainingGroupCopyController = $eventTrainingGroupCopyController;
        $this->orderPeopleCopyController = $orderPeopleCopyController;
        $this->filesCopyController = $filesCopyController;
        $this->expireCopyController = $expireCopyController;
        parent::__construct($id, $module, $config);
    }

    public function actionCopyAll(){
        $this->initCopyController->actionCopyAll();
        $this->documentInOutCopyController->actionCopyAll();
        $this->regulationCopyController->actionCopyAll();
        $this->documentOrderCopyController->actionCopyAll();
        $this->expireCopyController->actionCopyAll();
        $this->localResponsibilityCopyController->actionCopyAll();
        $this->orderPeopleCopyController->actionCopyAll();
        $this->foreignEventCopyController->actionCopyAll();
        $this->actCopyController->actionCopyAll();
        //$this->peopleTablesCopyController->actionCopyAll();
        $this->personalDataCopyController->actionCopyAll();
        $this->eventCopyController->actionCopyAll();
        $this->trainingProgramCopyController->actionCopyAll();
        $this->trainingGroupCopyController->actionCopyAll();
        $this->lessonThemeCopyController->actionCopyAll();
        $this->eventTrainingGroupCopyController->actionCopyAll();
        $this->certificateCopyController->actionCopyAll();
        $this->visitCopyController->actionCopyAll();
        $this->logCopyController->actionCopyAll();
        $this->filesCopyController->actionCopyAll();
    }

    public function actionDeleteAll()
    {
        $this->filesCopyController->actionDeleteAll();

        $this->participantAchievementCopyController->actionDeleteAll();
        $this->logCopyController->actionDeleteAll();

        $this->visitCopyController->actionDeleteAll();
        $this->certificateCopyController->actionDeleteAll();
        $this->eventTrainingGroupCopyController->actionDeleteAll();
        $this->lessonThemeCopyController->actionDeleteAll();
        $this->trainingGroupCopyController->actionDeleteAll();
        $this->trainingProgramCopyController->actionDeleteAll();
        $this->eventCopyController->actionDeleteAll();
        $this->personalDataCopyController->actionDeleteAll();
        $this->actCopyController->actionDeleteAll();
        $this->foreignEventCopyController->actionDeleteAll();
        $this->orderPeopleCopyController->actionDeleteAll();
        $this->localResponsibilityCopyController->actionDeleteAll();
        $this->expireCopyController->actionDeleteAll();
        $this->documentOrderCopyController->actionDeleteAll();
        $this->regulationCopyController->actionDeleteAll();
        $this->documentInOutCopyController->actionDeleteAll();
        $this->peopleTablesCopyController->actionDeleteAll();
        $this->initCopyController->actionDeleteAll();

    }
}