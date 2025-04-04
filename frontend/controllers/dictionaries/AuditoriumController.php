<?php

namespace frontend\controllers\dictionaries;

use common\components\traits\AccessControl;
use common\controllers\DocumentController;
use common\helpers\ButtonsFormatter;
use common\helpers\html\HtmlBuilder;
use common\repositories\dictionaries\AuditoriumRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\models\search\SearchAuditorium;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\services\dictionaries\AuditoriumService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * AuditoriumController implements the CRUD actions for Auditorium model.
 */
class AuditoriumController extends DocumentController
{
    use AccessControl;

    private AuditoriumRepository $repository;
    private AuditoriumService $service;

    public function __construct($id, $module, AuditoriumService $service, AuditoriumRepository $repository, $config = [])
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->service = $service;
        $this->repository = $repository;
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
     * Lists all Auditorium models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchAuditorium();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = ButtonsFormatter::primaryCreateLink('помещение');
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml
        ]);
    }

    /**
     * Displays a single Auditorium model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        /** @var AuditoriumWork $model */
        $model = $this->repository->get($id);
        $model->checkFilesExist();

        $links = ButtonsFormatter::updateDeleteLinks($id);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('view', [
            'model' => $model,
            'buttonsAct' => $buttonHtml
        ]);
    }

    /**
     * Creates a new Auditorium model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuditoriumWork();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $this->service->getFilesInstances($model);
            $this->repository->save($model);
            $this->service->saveFilesFromModel($model);
            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Auditorium model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        /** @var AuditoriumWork $model */
        $model = $this->repository->get($id);
        $tables = $this->service->getUploadedFilesTables($model);

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $this->service->getFilesInstances($model);
            $this->repository->save($model);
            $this->service->saveFilesFromModel($model);
            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'otherFiles' => $tables['other'],
        ]);
    }

    /**
     * Deletes an existing Auditorium model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        /** @var AuditoriumWork $model */
        $model = $this->repository->get($id);
        $this->repository->delete($model);

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
