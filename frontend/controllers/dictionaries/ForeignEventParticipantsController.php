<?php

namespace frontend\controllers\dictionaries;

use common\components\traits\AccessControl;
use common\components\wizards\LockWizard;
use common\helpers\ButtonsFormatter;
use common\helpers\ErrorAssociationHelper;
use common\helpers\html\HtmlBuilder;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\event\ParticipantAchievementRepository;
use common\repositories\general\ErrorsRepository;
use DomainException;
use frontend\events\foreign_event_participants\PersonalDataParticipantAttachEvent;
use frontend\forms\participants\MergeNewParticipantForm;
use frontend\forms\participants\MergeParticipantForm;
use frontend\models\search\SearchForeignEventParticipants;
use frontend\models\work\auxiliary\LoadParticipants;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonalDataParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\services\dictionaries\ForeignEventParticipantsService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * ForeignEventParticipantsController implements the CRUD actions for ForeignEventParticipants model.
 */
class ForeignEventParticipantsController extends Controller
{
    use AccessControl;

    public ForeignEventParticipantsRepository $repository;
    private TrainingGroupParticipantRepository $groupParticipantRepository;
    private SquadParticipantRepository $squadParticipantRepository;
    private ParticipantAchievementRepository $achievementRepository;
    private PersonalDataParticipantRepository $personalDataRepository;
    private ForeignEventParticipantsService $service;
    public ErrorsRepository $errorsRepository;
    private LockWizard $lockWizard;

    public function __construct(
        $id,
        $module,
        ForeignEventParticipantsRepository $repository,
        TrainingGroupParticipantRepository $groupParticipantRepository,
        SquadParticipantRepository         $squadParticipantRepository,
        ParticipantAchievementRepository   $achievementRepository,
        PersonalDataParticipantRepository  $personalDataRepository,
        ForeignEventParticipantsService    $service,
        ErrorsRepository $errorsRepository,
        LockWizard                         $lockWizard,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->groupParticipantRepository = $groupParticipantRepository;
        $this->squadParticipantRepository = $squadParticipantRepository;
        $this->achievementRepository = $achievementRepository;
        $this->personalDataRepository = $personalDataRepository;
        $this->service = $service;
        $this->errorsRepository = $errorsRepository;
        $this->lockWizard = $lockWizard;
    }

    public function actionIndex()
    {
        $searchModel = new SearchForeignEventParticipants();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = array_merge(
            ButtonsFormatter::primaryCreateLink('участника'),
            ButtonsFormatter::anyOneLink('Загрузить участников из файла', Yii::$app->frontUrls::PARTICIPANT_FILE_LOAD, ButtonsFormatter::BTN_SUCCESS),
            ButtonsFormatter::anyOneLink('Проверить участников на ошибки', Yii::$app->frontUrls::PARTICIPANT_ERROR_CHECK,ButtonsFormatter::BTN_DANGER)
        );
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml
        ]);
    }

    public function actionView($id)
    {
        /** @var ForeignEventParticipantsWork $model */
        $model = $this->repository->get($id);
        $model->fillPersonalDataRestrict($this->personalDataRepository->getPersonalDataRestrict($id));

        $links = ButtonsFormatter::updateDeleteLinks($id);
        $buttonHtml = HtmlBuilder::createGroupButton($links);
        $model->checkModel(ErrorAssociationHelper::getForeignEventParticipantErrorsList(), ForeignEventParticipantsWork::tableName(), $model->id);
        return $this->render('view', [
            'model' => $model,
            'buttonsAct' => $buttonHtml
        ]);
    }

    public function actionCreate()
    {
        $model = new ForeignEventParticipantsWork();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $this->repository->save($model);

            $model->recordEvent(new PersonalDataParticipantAttachEvent($model->id, $model->pd), PersonalDataParticipantWork::class);
            $model->releaseEvents();

            $this->service->checkCorrectOne($model);
            $model->checkModel(ErrorAssociationHelper::getForeignEventParticipantErrorsList(), ForeignEventParticipantsWork::tableName(), $model->id);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, ForeignEventParticipantsWork::tableName(), Yii::$app->user->id)) {
            /** @var ForeignEventParticipantsWork $model */
            $model = $this->repository->get($id);
            $model->fillPersonalDataRestrict($this->personalDataRepository->getPersonalDataRestrict($id));

            if ($model->load(Yii::$app->request->post())) {
                $this->lockWizard->unlockObject($id, ForeignEventParticipantsWork::tableName());
                if (!$model->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
                }

                $model->recordEvent(new PersonalDataParticipantAttachEvent($model->id, $model->pd), PersonalDataParticipantWork::class);
                $this->repository->save($model);
                $model->releaseEvents();

                $this->service->checkCorrectOne($model);
                $model->checkModel(ErrorAssociationHelper::getForeignEventParticipantErrorsList(), ForeignEventParticipantsWork::tableName(), $model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, ForeignEventParticipantsWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    /**
     * Deletes an existing ForeignEventParticipants model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        /** @var ForeignEventParticipantsWork $model */
        $model = $this->repository->get($id);
        $deleteErrors = $this->service->isAvailableDelete($id);

        if (count($deleteErrors) == 0) {
            $this->repository->delete($model);
            Yii::$app->session->addFlash('success', 'Участник деятельности "'.$model->getFIO(ForeignEventParticipantsWork::FIO_FULL).'" успешно удален');
        }
        else {
            Yii::$app->session->addFlash('error', implode('<br>', $deleteErrors));
        }

        return $this->redirect(['index']);
    }

    public function actionFileLoad()
    {
        $model = new LoadParticipants();
        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            $result = $model->ProcessParticipants();
            if (!empty($result['duplicates'])) {
                Yii::$app->session->set('emailChangeData', [
                    'originals' =>  ArrayHelper::toArray($result['originals']),
                    'duplicates' =>  ArrayHelper::toArray($result['duplicates'])
                ]);
                if ($result['fullDuplicatesCount'] > 0) {
                    Yii::$app->session->addFlash('info', "Найдено {$result['fullDuplicatesCount']} полных дубликатов, которые были пропущены.");
                }
                return $this->redirect(['confirm-email-changes']);

            }
            if ($result['fullDuplicatesCount'] > 0) {
                Yii::$app->session->addFlash('info', "Найдено {$result['fullDuplicatesCount']} полных дубликатов, которые были пропущены.");
            }
            //$this->service->checkCorrectAll();
            return $this->redirect(['index']);
        }

        return $this->render('file-load', [
            'model' => $model,
        ]);
    }

    protected function restoreParticipantsFromArray(array $data): array
    {
        $participants = [];
        foreach ($data as $item) {
            $participant = new ForeignEventParticipantsWork();
            Yii::configure($participant, $item);
            $participants[] = $participant;
        }
        return $participants;
    }

    protected function restoreExistingParticipantsFromArray(array $data): array
    {
        $participants = [];
        foreach ($data as $item) {
            $participant = $this->repository->get($item['id']);
            Yii::configure($participant, $item);
            $participants[] = $participant;
        }
        return $participants;
    }

    public function actionConfirmEmailChanges()
    {
        $sessionData = Yii::$app->session->get('emailChangeData', []);

        if (empty($sessionData['originals']) || empty($sessionData['duplicates'])) {
            Yii::$app->session->setFlash('error', 'Данные для подтверждения не найдены');
            return $this->redirect(['index']);
        }

        // Восстанавливаем объекты из массива
        $originals = $this->restoreExistingParticipantsFromArray($sessionData['originals']);
        $duplicates = $this->restoreParticipantsFromArray($sessionData['duplicates']);

        $model = new MergeNewParticipantForm(
            $this->repository,
            $originals,
            $duplicates
        );

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->changeSelectedEmails();
            Yii::$app->session->remove('emailChangeData');
            Yii::$app->session->setFlash('success', 'Изменения успешно применены');
            return $this->redirect(['index']);
        }

        return $this->render('merge-newparticipant', [
            'model' => $model,

        ]);
    }

    public function actionCheckCorrect()
    {
        $this->service->checkCorrectAll();
        return $this->redirect(['index']);
    }

    public function actionMergeParticipant()
    {
        $model = Yii::createObject(MergeParticipantForm::class);

        if ($model->load(Yii::$app->request->post()) && $model->editModel->load(Yii::$app->request->post())) {
            $model->save();
            Yii::$app->session->setFlash('success', 'Объединение произведено успешно!');
            return $this->redirect(['view', 'id' => $model->id1]);
        }

        return $this->render('merge-participant', [
            'model' => $model,
        ]);
    }

    public function actionInfo($id1, $id2)
    {
        $p1 = $this->repository->get($id1);
        $p2 = $this->repository->get($id2);
        $groups1 = $this->groupParticipantRepository->getByParticipantIds([$id1]);
        $groups2 = $this->groupParticipantRepository->getByParticipantIds([$id2]);
        $events1 = $this->squadParticipantRepository->getAllByParticipantId($id1);
        $events2 = $this->squadParticipantRepository->getAllByParticipantId($id2);
        $achieves1 = $this->achievementRepository->getByParticipantId($id1);
        $achieves2 = $this->achievementRepository->getByParticipantId($id2);
        $personalData1 = $this->personalDataRepository->getPersonalDataByParticipantId($id1);
        $personalData2 = $this->personalDataRepository->getPersonalDataByParticipantId($id2);

        return HtmlBuilder::createMergeParticipantsTable(
            $p1,
            $p2,
            $groups1,
            $groups2,
            $events1,
            $events2,
            $achieves1,
            $achieves2,
            $personalData1,
            $personalData2,
        );
    }

    /**
     * Finds the ForeignEventParticipants model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ForeignEventParticipantsWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ForeignEventParticipantsWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionErrorCheck()
    {
        set_time_limit(300);
        Yii::$container->set('yii\web\Response', [
            'timeout' => 300,
        ]);
        /** @var $participant ForeignEventParticipantsWork **/
        $errors = $this->errorsRepository->getErrorByTableName(ForeignEventParticipantsWork::tableName());
        $participants = ForeignEventParticipantsWork::find()->where(['NOT IN', 'id', ArrayHelper::getColumn($errors, 'table_row_id')])->all();
        foreach ($participants as $participant)
        {
            $participant->checkModel(ErrorAssociationHelper::getForeignEventParticipantErrorsList(), ForeignEventParticipantsWork::tableName(), $participant->id);
        }
        set_time_limit(60);
        Yii::$app->session->setFlash('success', 'Проверка на ошибки произведена успешно!');
        return $this->redirect(['index']);
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
