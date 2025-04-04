<?php

namespace frontend\controllers\event;

use common\components\traits\AccessControl;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\helpers\ButtonsFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\SortHelper;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\event\EventRepository;
use common\repositories\general\FilesRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\event\CreateEventBranchEvent;
use frontend\events\event\CreateEventScopeEvent;
use frontend\models\search\SearchEvent;
use frontend\models\work\event\EventWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\services\event\EventService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * EventController implements the CRUD actions for Event model.
 */
class OurEventController extends DocumentController
{
    use AccessControl;

    private EventRepository $repository;
    private EventService $service;
    private PeopleRepository $peopleRepository;
    private RegulationRepository $regulationRepository;
    private LockWizard $lockWizard;

    public function __construct(
        $id,
        $module,
        EventRepository $repository,
        EventService $service,
        PeopleRepository $peopleRepository,
        RegulationRepository $regulationRepository,
        LockWizard $lockWizard,
        $config = [])
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->repository = $repository;
        $this->service = $service;
        $this->peopleRepository = $peopleRepository;
        $this->regulationRepository = $regulationRepository;
        $this->lockWizard = $lockWizard;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Event models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchEvent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = ButtonsFormatter::primaryCreateLink('мероприятие');
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    /**
     * Displays a single Event model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $links = ButtonsFormatter::updateDeleteLinks($id);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        /** @var EventWork $model */
        $model = $this->repository->get($id);
        $model->checkFilesExist();

        return $this->render('view', [
            'model' => $model,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EventWork();

        if ($model->load(Yii::$app->request->post())) {
            $this->service->getPeopleStamps($model);
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $this->service->getFilesInstances($model);

            $this->repository->save($model);
            $this->service->saveFilesFromModel($model);

            $model->recordEvent(new CreateEventBranchEvent($model->id, $model->branches), get_class($model));
            $model->recordEvent(new CreateEventScopeEvent($model->id, $model->scopes), get_class($model));
            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'people' => $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO, SORT_ASC),
            'regulations' => $this->regulationRepository->getOrderedList(),
            'branches' => ArrayHelper::getColumn($this->repository->getBranches($model->id), 'branch'),
        ]);
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, ForeignEventWork::tableName(), Yii::$app->user->id)) {
            /** @var EventWork $model */
            $model = $this->repository->get($id);
            $model->fillSecondaryFields();
            $model->setValuesForUpdate();

            $tables = $this->service->getUploadedFilesTables($model);

            if ($model->load(Yii::$app->request->post())) {
                $this->lockWizard->unlockObject($id, ForeignEventWork::tableName());
                if (!$model->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
                }

                $this->service->getFilesInstances($model);

                $this->repository->save($model);
                $this->service->saveFilesFromModel($model);

                $model->recordEvent(new CreateEventBranchEvent($model->id, $model->branches), get_class($model));
                $model->recordEvent(new CreateEventScopeEvent($model->id, $model->scopes), get_class($model));
                $model->releaseEvents();

                return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('update', [
                'model' => $model,
                'people' => $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO, SORT_ASC),
                'regulations' => $this->regulationRepository->getOrderedList(),
                'branches' => ArrayHelper::getColumn($this->repository->getBranches($model->id), 'branch'),
                'protocolFiles' => $tables['protocol'],
                'photoFiles' => $tables['photo'],
                'reportingFiles' => $tables['report'],
                'otherFiles' => $tables['other'],
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, ForeignEventWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        /** @var EventWork $model */
        $model = $this->repository->get($id);
        $deleteErrors = $this->service->isAvailableDelete($id);

        if (count($deleteErrors) == 0) {
            $this->repository->delete($model);
            Yii::$app->session->addFlash('success', 'Событие "'.$model->name.'" успешно удалено');
        }
        else {
            Yii::$app->session->addFlash('error', implode('<br>', $deleteErrors));
        }

        return $this->redirect(['index']);
    }

    /*public function actionDeleteGroup($id, $modelId)
    {
        $group = EventTrainingGroupWork::find()->where(['id' => $id])->one();
        $group->delete();
        return $this->redirect('index?r=event/update&id='.$modelId);
    }

    public function actionDeleteExternalEvent($id, $modelId)
    {
        $eventsLink = EventsLinkWork::find()->where(['id' => $id])->one();
        $eventsLink->delete();
        return $this->redirect('index?r=event/update&id='.$modelId);
    }

    public function actionAmnesty ($id)
    {
        $errorsAmnesty = new EventErrorsWork();
        $errorsAmnesty->EventAmnesty($id);
        return $this->redirect('index?r=event/view&id='.$id);
    }*/

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
