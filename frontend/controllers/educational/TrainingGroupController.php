<?php

namespace frontend\controllers\educational;

use common\components\logger\base\LogInterface;
use common\components\logger\LogFactory;
use common\components\traits\AccessControl;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\helpers\ButtonsFormatter;
use common\helpers\common\RequestHelper;
use common\helpers\ErrorAssociationHelper;
use common\helpers\files\FilePaths;
use common\helpers\html\HtmlBuilder;
use common\Model;
use common\repositories\dictionaries\AuditoriumRepository;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\general\FilesRepository;
use common\services\general\errors\ErrorService;
use common\services\general\files\FileService;
use DomainException;
use frontend\components\creators\ExcelCreator;
use frontend\events\visit\DeleteLessonFromVisitEvent;
use frontend\forms\journal\JournalForm;
use frontend\forms\training_group\PitchGroupForm;
use frontend\forms\training_group\ProtocolForm;
use frontend\forms\training_group\TrainingGroupBaseForm;
use frontend\forms\training_group\TrainingGroupCombinedForm;
use frontend\forms\training_group\TrainingGroupParticipantForm;
use frontend\forms\training_group\TrainingGroupScheduleForm;
use frontend\invokables\JournalLoader;
use frontend\invokables\PlanLoad;
use frontend\invokables\ProtocolLoader;
use frontend\models\search\SearchTrainingGroup;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\ProjectThemeWork;
use frontend\services\educational\GroupDocumentService;
use frontend\services\educational\GroupLessonService;
use frontend\services\educational\GroupProjectThemesService;
use frontend\services\educational\JournalService;
use frontend\services\educational\TrainingGroupService;
use Yii;

class TrainingGroupController extends DocumentController
{
    use AccessControl;

    private TrainingGroupService $service;
    private JournalService $journalService;
    private TrainingProgramRepository $trainingProgramRepository;
    private TrainingGroupRepository $trainingGroupRepository;
    private TrainingGroupLessonRepository $groupLessonRepository;
    private ForeignEventParticipantsRepository $participantsRepository;
    private PeopleRepository $peopleRepository;
    private AuditoriumRepository $auditoriumRepository;
    private LessonThemeRepository $lessonThemeRepository;
    private GroupProjectThemesService $projectThemesService;
    private GroupLessonService $lessonService;
    private LockWizard $lockWizard;
    private GroupDocumentService $documentService;
    private ErrorService $errorService;

    public function __construct(
        $id,
        $module,
        FileService $fileService,
        FilesRepository $filesRepository,
        TrainingGroupService $service,
        JournalService $journalService,
        TrainingProgramRepository $trainingProgramRepository,
        TrainingGroupRepository $trainingGroupRepository,
        TrainingGroupLessonRepository $groupLessonRepository,
        ForeignEventParticipantsRepository $participantsRepository,
        PeopleRepository $peopleRepository,
        AuditoriumRepository $auditoriumRepository,
        LessonThemeRepository $lessonThemeRepository,
        GroupProjectThemesService $projectThemesService,
        GroupLessonService $lessonService,
        LockWizard $lockWizard,
        GroupDocumentService $documentService,
        ErrorService $errorService,
        $config = [])
    {
        parent::__construct($id, $module, $fileService, $filesRepository, $config);
        $this->service = $service;
        $this->journalService = $journalService;
        $this->trainingProgramRepository = $trainingProgramRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->groupLessonRepository = $groupLessonRepository;
        $this->participantsRepository = $participantsRepository;
        $this->peopleRepository = $peopleRepository;
        $this->auditoriumRepository = $auditoriumRepository;
        $this->lessonThemeRepository = $lessonThemeRepository;
        $this->projectThemesService = $projectThemesService;
        $this->lessonService = $lessonService;
        $this->lockWizard = $lockWizard;
        $this->documentService = $documentService;
        $this->errorService = $errorService;
    }


    public function actionIndex()
    {
        $searchModel = new SearchTrainingGroup();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = array_merge(
            ButtonsFormatter::anyOneLink('Добавить программу', 'create', ButtonsFormatter::BTN_PRIMARY),
            ButtonsFormatter::anyOneLink('Изменить актуальность', Yii::$app->frontUrls::TRAINING_GROUP_ARCHIVE, ButtonsFormatter::BTN_SUCCESS)
        );
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    public function actionCreate()
    {
        $form = new TrainingGroupBaseForm();
        $modelTeachers = [new TeacherGroupWork];
        $programs = $this->trainingProgramRepository->getAllActual();
        $people = $this->peopleRepository->getPeopleFromMainCompany();

        if ($form->load(Yii::$app->request->post())) {
            if (!$form->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($form->getErrors()));
            }
            $groupModel = $this->service->convertBaseFormToModel($form);

            $modelTeachers = Model::createMultiple(TeacherGroupWork::classname());
            Model::loadMultiple($modelTeachers, Yii::$app->request->post());
            if (Model::validateMultiple($modelTeachers, ['id'])) {
                $form->teachers = $modelTeachers;
                $groupModel->generateNumber($this->peopleRepository->get($form->teachers[0]->peopleId));
            }
            else {
                $groupModel->generateNumber('');
            }

            $form->id = $this->trainingGroupRepository->save($groupModel);
            $this->service->attachTeachers($form, $form->teachers);

            $this->service->getFilesInstances($form);
            $this->service->saveFilesFromModel($form);
            $form->releaseEvents();
            $groupModel->checkModel(ErrorAssociationHelper::getTrainingGroupErrorsList(), TrainingGroupWork::tableName(), $groupModel->id);

            return $this->redirect(['view', 'id' => $groupModel->id]);
        }

        return $this->render('create', [
            'model' => $form,
            'modelTeachers' => $modelTeachers,
            'trainingPrograms' => $programs,
            'people' => $people,
        ]);
    }

    /**
     * Страница с изменением статуса архивирования учебных групп
     * @return string
     */
    public function actionArchive()
    {
        $searchModel = new SearchTrainingGroup(TrainingGroupWork::NO_ARCHIVE);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $links = ButtonsFormatter::anyOneLink('Сохранить архив', '', ButtonsFormatter::BTN_PRIMARY, 'archive-save');
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('archive', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    public function actionArchiveGroup($id)
    {
        /** @var TrainingGroupWork $model */
        $model = $this->trainingGroupRepository->get($id);
        if (!$this->errorService->isGroupRestrict($id)) {
            $model->setArchive(TrainingGroupWork::IS_ARCHIVE);
            $this->trainingGroupRepository->save($model);
            Yii::$app->session->setFlash('success', 'Группа отправлена в архив');
        }
        else {
            Yii::$app->session->setFlash('error', 'Нельзя отправить в архив группу с ошибками');
        }


        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionUnarchiveGroup($id)
    {
        /** @var TrainingGroupWork $model */
        $model = $this->trainingGroupRepository->get($id);
        $model->setArchive(TrainingGroupWork::NO_ARCHIVE);
        $this->trainingGroupRepository->save($model);
        Yii::$app->session->setFlash('success', 'Группа извлечена из архива');

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Сохранение статусов архивов учебных групп
     * @return false|string
     */
    public function actionArchiveSave()
    {
        $archive = Yii::$app->request->post('unactual');
        $nonArchive = Yii::$app->request->post('actual');

        $archive = is_array($archive) ? $archive : [];
        $nonArchive = is_array($nonArchive) ? $nonArchive : [];

        $errors = $this->service->setGroupArchive($archive, $nonArchive);
        if (count($errors) > 0) {
            Yii::$app->session->setFlash('error', implode('<br>', $errors));
        }

        return json_encode(['success' => true]);
    }

    public function actionBaseForm($id)
    {
        if ($this->lockWizard->lockObject($id, TrainingGroupWork::tableName(), Yii::$app->user->id)) {
            /** @var TrainingGroupWork $model */
            $model = $this->trainingGroupRepository->get($id);
            if (!$model->isArchive()) {
                $formBase = new TrainingGroupBaseForm($id);
                $programs = $this->trainingProgramRepository->getAllActual();
                $people = $this->peopleRepository->getPeopleFromMainCompany();
                $tables = $this->service->getUploadedFilesTables($formBase);

                if ($formBase->load(Yii::$app->request->post())) {
                    $this->lockWizard->unlockObject($id, TrainingGroupWork::tableName());
                    if (!$formBase->validate()) {
                        throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($formBase->getErrors()));
                    }
                    $groupModel = $this->service->convertBaseFormToModel($formBase);

                    $modelTeachers = Model::createMultiple(TeacherGroupWork::classname());
                    Model::loadMultiple($modelTeachers, Yii::$app->request->post());
                    if (Model::validateMultiple($modelTeachers, ['peopleId'])) {
                        $formBase->teachers = $modelTeachers;
                        $groupModel->generateNumber($this->peopleRepository->get($formBase->teachers[0]->peopleId));
                    } else {
                        $groupModel->generateNumber('');
                    }

                    $formBase->id = $this->trainingGroupRepository->save($groupModel);
                    $this->service->attachTeachers($formBase, $formBase->teachers);

                    $this->service->getFilesInstances($formBase);
                    $this->service->saveFilesFromModel($formBase);
                    $formBase->releaseEvents();

                    $groupModel->checkModel(ErrorAssociationHelper::getTrainingGroupErrorsList(), TrainingGroupWork::tableName(), $groupModel->id);

                    return $this->redirect(['view', 'id' => $groupModel->id]);
                }

                return $this->render('_form-base', [
                    'model' => $formBase,
                    'modelTeachers' => count($formBase->teachers) > 0 ? $formBase->teachers : [new TeacherGroupWork],
                    'trainingPrograms' => $programs,
                    'people' => $people,
                    'photos' => $tables['photos'],
                    'presentations' => $tables['presentations'],
                    'workMaterials' => $tables['workMaterials'],
                ]);
            }
            else {
                Yii::$app->session->setFlash('error', 'Невозможно редактировать архивную группу');
                return $this->redirect(Yii::$app->request->referrer ?: ['index']);
            }
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, TrainingGroupWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionParticipantForm($id)
    {
        if ($this->lockWizard->lockObject($id, TrainingGroupWork::tableName(), Yii::$app->user->id)) {
            /** @var TrainingGroupWork $model */
            $model = $this->trainingGroupRepository->get($id);
            if (!$model->isArchive()) {
                $formParticipant = new TrainingGroupParticipantForm($id);
                $childs = $this->participantsRepository->getSortedList(ForeignEventParticipantsRepository::SORT_FIO);

                if (count(Yii::$app->request->post()) > 0) {
                    $this->lockWizard->unlockObject($id, TrainingGroupWork::tableName());
                    $modelChilds = Model::createMultiple(TrainingGroupParticipantWork::classname());
                    Model::loadMultiple($modelChilds, Yii::$app->request->post());
                    if (Model::validateMultiple($modelChilds, ['id', 'participant_id', 'send_method'])) {
                        $formParticipant->participants = $modelChilds;
                    }

                    $this->service->attachParticipants($formParticipant);
                    $formParticipant->releaseEvents();
                    $this->service->refreshVisitsByParticipants($id);
                    $formParticipant->group->checkModel(ErrorAssociationHelper::getTrainingGroupErrorsList(), TrainingGroupWork::tableName(), $formParticipant->group->id);

                    return $this->redirect(['view', 'id' => $formParticipant->id]);
                }

                return $this->render('_form-participant', [
                    'model' => $formParticipant,
                    'modelChilds' => count($formParticipant->participants) > 0 ? $formParticipant->participants : [new TrainingGroupParticipantWork],
                    'childs' => $childs
                ]);
            }
            else {
                Yii::$app->session->setFlash('error', 'Невозможно редактировать архивную группу');
                return $this->redirect(Yii::$app->request->referrer ?: ['index']);
            }
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, TrainingGroupWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionScheduleForm($id)
    {
        if ($this->lockWizard->lockObject($id, TrainingGroupWork::tableName(), Yii::$app->user->id)) {
            /** @var TrainingGroupWork $model */
            $model = $this->trainingGroupRepository->get($id);
            if (!$model->isArchive()) {
                $formData = $this->service->prepareFormScheduleData($id);
                /** @var TrainingGroupScheduleForm $formSchedule */
                $formSchedule = $formData['formSchedule'];
                $modelLessons = $formData['modelLessons'];
                $auditoriums = $formData['auditoriums'];
                $scheduleTable = $formData['scheduleTable'];

                if ($formSchedule->load(Yii::$app->request->post())) {
                    $this->lockWizard->unlockObject($id, TrainingGroupWork::tableName());
                    $modelLessons = Model::createMultiple(TrainingGroupLessonWork::classname());
                    Model::loadMultiple($modelLessons, Yii::$app->request->post());
                    if (Model::validateMultiple($modelLessons, ['lesson_date', 'lesson_start_time', 'branch', 'auditorium_id', 'autoDate'])) {
                        $formSchedule->lessons = $modelLessons;
                    }

                    if (!$formSchedule->isManual()) {
                        $formSchedule->convertPeriodToLessons();
                    }

                    $this->service->preprocessingLessons($formSchedule);
                    $this->service->attachLessons($formSchedule);
                    $formSchedule->releaseEvents();

                    $formSchedule->trainingGroup->checkModel(ErrorAssociationHelper::getTrainingGroupErrorsList(), TrainingGroupWork::tableName(), $formSchedule->trainingGroup->id);

                    return $this->redirect(['view', 'id' => $formSchedule->id]);
                }

                return $this->render('_form-schedule', [
                    'model' => $formSchedule,
                    'modelLessons' => count($modelLessons) > 0 ? $modelLessons : [new TrainingGroupParticipantWork],
                    'auditoriums' => $auditoriums,
                    'scheduleTable' => $scheduleTable
                ]);
            }
            else {
                Yii::$app->session->setFlash('error', 'Невозможно редактировать архивную группу');
                return $this->redirect(Yii::$app->request->referrer ?: ['index']);
            }
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, TrainingGroupWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionPitchForm($id)
    {
        if ($this->lockWizard->lockObject($id, TrainingGroupWork::tableName(), Yii::$app->user->id)) {
            /** @var TrainingGroupWork $model */
            $model = $this->trainingGroupRepository->get($id);
            if (!$model->isArchive()) {
                $formPitch = new PitchGroupForm($id);
                $peoples = $this->peopleRepository->getPeopleFromMainCompany();

                if ($formPitch->load(Yii::$app->request->post())) {
                    $this->lockWizard->unlockObject($id, TrainingGroupWork::tableName());
                    if (!$formPitch->validate()) {
                        throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($formPitch->getErrors()));
                    }

                    $modelThemes = Model::createMultiple(ProjectThemeWork::classname());
                    Model::loadMultiple($modelThemes, Yii::$app->request->post());
                    if (Model::validateMultiple($modelThemes, ['id', 'name', 'project_type', 'description'])) {
                        $formPitch->themes = $modelThemes;
                    }

                    $modelExperts = Model::createMultiple(TrainingGroupExpertWork::classname());
                    Model::loadMultiple($modelExperts, Yii::$app->request->post());
                    if (Model::validateMultiple($modelExperts, ['id', 'expertId', 'expert_type'])) {
                        $formPitch->experts = $modelExperts;
                    }

                    $this->service->createNewThemes($formPitch);
                    $this->service->attachThemes($formPitch);
                    $this->service->attachExperts($formPitch);
                    $formPitch->releaseEvents();
                    $formPitch->save();

                    $formPitch->entity->checkModel(ErrorAssociationHelper::getTrainingGroupErrorsList(), TrainingGroupWork::tableName(), $formPitch->entity->id);

                    return $this->redirect(['view', 'id' => $formPitch->id]);
                }

                return $this->render('_form-pitch', [
                    'model' => $formPitch,
                    'peoples' => $peoples
                ]);
            }
            else {
                Yii::$app->session->setFlash('error', 'Невозможно редактировать архивную группу');
                return $this->redirect(Yii::$app->request->referrer ?: ['index']);
            }
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, TrainingGroupWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionCreateLessonThemes($groupId)
    {
        $result = $this->service->createLessonThemes($groupId);
        if ($result === TrainingGroupWork::ERROR_NO_PROGRAM) {
            Yii::$app->session->setFlash('danger', 'Ошибка создания тематического плана: у группы отсутствует образовательная программа');
        }
        if ($result === TrainingGroupWork::ERROR_THEMES_MISMATCH) {
            Yii::$app->session->setFlash('danger', 'Ошибка создания тематического плана: количество занятий группы не совпадает с количеством тем в образовательной программе');
        }

        if ($result === true) {
            Yii::$app->session->setFlash('success', 'Тематический план создан');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUpdateLesson($groupId, $entityId)
    {
        /** @var TrainingGroupLessonWork $model */
        $model = $this->groupLessonRepository->get($entityId);
        $auditoriums = $this->auditoriumRepository->getByBranch($model->branch);

        if ($model->load(Yii::$app->request->post())) {
            $this->groupLessonRepository->save($model);

            return $this->redirect(['schedule-form', 'id' => $groupId]);
        }

        return $this->render('update-lesson', [
            'model' => $model,
            'auds' => $auditoriums
        ]);
    }

    public function actionDeleteLesson($groupId, $entityId)
    {
        /** @var TrainingGroupLessonWork $model */
        $result = $this->lessonService->delete($entityId);

        if ($result) {
            Yii::$app->session->setFlash('success', 'Занятие успешно удалено');
        }
        else {
            Yii::$app->session->setFlash('danger', 'Ошибка удаления занятия');
        }

        $model->releaseEvents();
        return $this->redirect(['schedule-form', 'id' => $groupId]);
    }

    public function actionDeleteTheme($groupId, $entityId)
    {
        $result = $this->projectThemesService->delete($entityId);
        if (!$result) {
            Yii::$app->session->setFlash('danger', 'Невозможно удалить тему, т.к. она связана с одним или несколькими обучающимися в журнале');
        }

        return $this->redirect(['pitch-form', 'id' => $groupId]);
    }

    public function actionView($id)
    {
        $form = new TrainingGroupCombinedForm($id);

        $links = ButtonsFormatter::updateDeleteLinks($id, Yii::$app->frontUrls::TRAINING_GROUP_UPDATE);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('view', [
            'model' => $form,
            'journalState' => $this->journalService->checkJournalStatus($id),
            'buttonsAct' => $buttonHtml
        ]);
    }

    public function actionGenerateJournal($id)
    {
        $result = $this->journalService->createJournal($id);
        if ($result) {
            Yii::$app->session->setFlash('success', 'Журнал успешно создан');
        }
        else {
            Yii::$app->session->setFlash('danger', 'Ошибка создания журнала');
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDeleteJournal($id)
    {
        $result = $this->journalService->deleteJournal($id);
        if ($result) {
            Yii::$app->session->setFlash('success', 'Журнал успешно удален');
        }
        else {
            Yii::$app->session->setFlash('danger', 'Ошибка удаления журнала');
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDelete($id)
    {
        /** @var TrainingGroupWork $model */
        $model = $this->trainingGroupRepository->get($id);
        $deleteErrors = $this->service->isAvailableDelete($id);

        if (count($deleteErrors) == 0) {
            $this->trainingGroupRepository->delete($model);
            Yii::$app->session->addFlash('success', 'Группа "'.$model->number.'" успешно удалена');
        }
        else {
            Yii::$app->session->addFlash('error', implode('<br>', $deleteErrors));
        }

        return $this->redirect(['index']);
    }

    public function actionGroupDeletion($id)
    {
        $errorString = '';
        $data = RequestHelper::getDataFromPost(Yii::$app->request->post(), 'check', RequestHelper::CHECKBOX);
        foreach ($data as $item) {
            $result = $this->lessonService->delete($item);
            if (!$result) {
                $errorString .= "Ошибка удаления занятия (ID: $item)<br>";
            }
        }
        Yii::$app->session->setFlash('danger', $errorString);
        return $this->redirect(['schedule-form', 'id' => $id]);
    }

    public function actionDownloadPlan($id)
    {
        /** @var TrainingGroupWork $group */
        $group = $this->trainingGroupRepository->get($id);
        $lessonThemes = $this->lessonThemeRepository->getByTrainingGroupId($id);
        $loader = new PlanLoad(
            $lessonThemes,
            $group->number
        );
        $loader();
    }

    public function actionCreateProtocol($id)
    {
        $model = new ProtocolForm(
            $this->trainingGroupRepository->get($id)
        );

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $loader = new ProtocolLoader(
                    $this->documentService->generateProtocol($model),
                    "Протокол итоговой аттестации группы {$model->group->number}"
                );
                $loader();

                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('protocol-settings', [
            'model' => $model,
        ]);
    }

    public function actionDownloadJournal($id)
    {
        $model = $this->trainingGroupRepository->get($id);
        $loader = new JournalLoader(
            $this->documentService->generateJournal($id),
            "Журнал группы $model->number.xlsx"
        );
        $loader();
    }

    public function actionAmnesty($id)
    {
        $this->errorService->amnestyErrors(TrainingGroupWork::tableName(), $id);
        Yii::$app->session->setFlash('success', 'Ошибки успешно прощены');

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionSubAuds()
    {
        $result = HtmlBuilder::createEmptyOption('Вне отдела');
        if ($branch = Yii::$app->request->post('branch')) {
            if (array_key_exists($branch, Yii::$app->branches->getOnlyEducational())) {
                $result .= HtmlBuilder::buildOptionList($this->auditoriumRepository->getByBranch($branch));
            }
        }

        echo $result;
    }

    public function beforeAction($action)
    {
        $result = $this->checkActionAccess($action);
        if ($result['url'] !== '') {
            $this->redirect($result['url']);
            return $result['status'];
        }

        return parent::beforeAction($action);
    }
}