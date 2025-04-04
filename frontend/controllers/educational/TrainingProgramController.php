<?php

namespace frontend\controllers\educational;

use app\components\DynamicWidget;
use common\components\logger\base\LogInterface;
use common\components\logger\LogFactory;
use common\components\traits\AccessControl;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\helpers\ButtonsFormatter;
use common\helpers\ErrorAssociationHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use common\models\work\LogWork;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\educational\training_program\CreateTrainingProgramBranchEvent;
use frontend\models\search\SearchTrainingProgram;
use frontend\models\work\educational\training_program\ThematicPlanWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\services\educational\TrainingProgramService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * TrainingProgramController implements the CRUD actions for TrainingProgram model.
 */
class TrainingProgramController extends DocumentController
{
    use AccessControl;

    private TrainingProgramService $service;
    private TrainingProgramRepository $repository;
    private PeopleRepository $peopleRepository;
    private LockWizard $lockWizard;

    public function __construct(
        $id,
        $module,
        TrainingProgramService $service,
        TrainingProgramRepository $repository,
        PeopleRepository $peopleRepository,
        LockWizard $lockWizard,
        $config = []
    )
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->service = $service;
        $this->repository = $repository;
        $this->peopleRepository = $peopleRepository;
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
                    'relevance-save' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Страница с изменением статуса актуальности образовательных программ
     * @return string
     */
    public function actionRelevance()
    {
        $searchModel = new SearchTrainingProgram(1);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $links = ButtonsFormatter::anyOneLink('Сохранить статус', '', ButtonsFormatter::BTN_PRIMARY, 'relevance-save');
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('relevance', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    public function actionRelevanceSave()
    {
        $actual = Yii::$app->request->post('actual');
        $unactual = Yii::$app->request->post('unactual');

        $actual = is_array($actual) ? $actual : [];
        $unactual = is_array($unactual) ? $unactual : [];

        $this->service->setProgramRelevance($actual, $unactual);

        return json_encode(['success' => true]);
    }

    /**
     * Lists all TrainingProgram models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchTrainingProgram();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = array_merge(
            ButtonsFormatter::anyOneLink('Добавить программу', 'create', 'btn-primary'),
            ButtonsFormatter::anyOneLink('Изменить актуальность', Yii::$app->frontUrls::PROGRAM_RELEVANCE, ButtonsFormatter::BTN_SUCCESS)
        );
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    /**
     * Displays a single TrainingProgram model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $links = ButtonsFormatter::updateDeleteLinks($id);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        /** @var TrainingProgramWork $model */
        $model = $this->repository->get($id);
        $model->checkFilesExist();

        return $this->render('view', [
            'model' => $model,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    /**
     * Creates a new TrainingProgram model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrainingProgramWork();
        $ourPeople = $this->peopleRepository->getPeopleFromMainCompany();

        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $postThemes = DynamicWidget::getData(basename(TrainingProgramWork::class), 'themes', $post);
            $postControls = DynamicWidget::getData(basename(TrainingProgramWork::class), 'controls', $post);
            $postAuthors = DynamicWidget::getData(basename(TrainingProgramWork::class), 'authors', $post);
            $this->service->getFilesInstances($model);
            $this->repository->save($model);

            $this->service->attachUtp($model, $postThemes, $postControls);
            $this->service->saveFilesFromModel($model);
            $this->service->saveUtpFromFile($model);
            $this->service->attachAuthors($model, $postAuthors);

            $model->recordEvent(new CreateTrainingProgramBranchEvent($model->id, $model->branches), TrainingProgramWork::class);
            $model->releaseEvents();
            $model->checkModel(ErrorAssociationHelper::getTrainingProgramErrorsList(), TrainingProgramWork::tableName(), $model->id);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'ourPeople' => $ourPeople,
        ]);
    }

    /**
     * Updates an existing TrainingProgram model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, TrainingProgramWork::tableName(), Yii::$app->user->id)) {
            /** @var TrainingProgramWork $model */
            $model = $this->repository->get($id);
            $model->setBranches();
            $authors = $this->repository->getAuthors($id);
            $themes = $this->repository->getThematicPlan($id);
            $fileTables = $this->service->getUploadedFilesTables($model);
            $depTables = $this->service->getDependencyTables($authors, $themes);
            $ourPeople = $this->peopleRepository->getPeopleFromMainCompany();

            $post = Yii::$app->request->post();
            if ($model->load($post)) {
                $this->lockWizard->unlockObject($id, TrainingProgramWork::tableName());
                if (!$model->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
                }

                $postThemes = DynamicWidget::getData(basename(TrainingProgramWork::class), 'themes', $post);
                $postControls = DynamicWidget::getData(basename(TrainingProgramWork::class), 'controls', $post);
                $postAuthors = DynamicWidget::getData(basename(TrainingProgramWork::class), 'authors', $post);
                $this->service->getFilesInstances($model);
                $this->repository->save($model);

                $this->service->attachUtp($model, $postThemes, $postControls);
                $this->service->saveFilesFromModel($model);
                $this->service->saveUtpFromFile($model);
                $this->service->attachAuthors($model, $postAuthors);

                $model->recordEvent(new CreateTrainingProgramBranchEvent($model->id, $model->branches), TrainingProgramWork::class);
                $model->releaseEvents();
                $model->checkModel(ErrorAssociationHelper::getTrainingProgramErrorsList(), TrainingProgramWork::tableName(), $model->id);

                return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('update', [
                'model' => $model,
                'ourPeople' => $ourPeople,
                'modelAuthor' => $depTables['authors'],
                'modelThematicPlan' => $depTables['themes'],
                'mainFile' => $fileTables['main'],
                'docFiles' => $fileTables['doc'],
                'contractFile' => $fileTables['contract'],
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, TrainingProgramWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    /**
     * Deletes an existing TrainingProgram model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        /** @var TrainingProgramWork $model */
        $model = $this->repository->get($id);
        $deleteErrors = $this->service->isAvailableDelete($id);

        if (count($deleteErrors) == 0) {
            $this->repository->delete($model);
            Yii::$app->session->addFlash('success', 'Образовательная программа "'.$model->name.'" успешно удалена');
        }
        else {
            Yii::$app->session->addFlash('error', implode('<br>', $deleteErrors));
        }

        return $this->redirect(['index']);
    }

    public function actionUpdateTheme($id, $modelId)
    {
        /** @var ThematicPlanWork $model */
        $model = $this->repository->getTheme($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->repository->saveTheme($model);
            return $this->redirect(['educational/training-program/update', 'id' => $modelId]);
        }
        return $this->render('update-theme', [
            'model' => $model,
        ]);
    }

    public function actionDeleteTheme($id, $modelId)
    {
        /** @var ThematicPlanWork $plan */
        //$name = $plan->trainingProgramWork->name;
        $this->repository->deleteTheme($id);

        return $this->redirect(['educational/training-program/update', 'id' => $modelId]);
    }

    public function actionDeleteAuthor($id, $modelId)
    {
        $this->repository->deleteAuthor($id);

        return $this->redirect(['educational/training-program/update', 'id' => $modelId]);
    }

    private function InArray($id, $array)
    {
        for ($i = 0; $i < count($array); $i++)
            if ($id == $array[$i])
                return true;
        return false;
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
