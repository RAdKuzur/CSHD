<?php

namespace frontend\controllers\responsibility;

use common\components\BaseConsts;
use common\components\traits\AccessControl;
use common\helpers\ButtonsFormatter;
use common\helpers\html\HtmlBuilder;
use common\repositories\dictionaries\AuditoriumRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\order\OrderMainRepository;
use common\repositories\regulation\RegulationRepository;
use common\repositories\responsibility\LegacyResponsibleRepository;
use common\repositories\responsibility\LocalResponsibilityRepository;
use common\services\general\PeopleStampService;
use DomainException;
use frontend\forms\ResponsibilityForm;
use frontend\models\search\SearchLocalResponsibility;
use frontend\models\work\responsibility\LegacyResponsibleWork;
use frontend\models\work\responsibility\LocalResponsibilityWork;
use frontend\services\responsibility\LocalResponsibilityService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LocalResponsibilityController implements the CRUD actions for LocalResponsibility model.
 */
class LocalResponsibilityController extends Controller
{
    use AccessControl;

    private LocalResponsibilityRepository $responsibilityRepository;
    private LegacyResponsibleRepository $legacyRepository;
    private AuditoriumRepository $auditoriumRepository;
    private PeopleRepository $peopleRepository;
    private OrderMainRepository $orderRepository;
    private RegulationRepository $regulationRepository;

    private LocalResponsibilityService $service;
    private PeopleStampService $peopleStampService;

    public function __construct($id, $module,
                                LocalResponsibilityRepository $responsibilityRepository,
                                LegacyResponsibleRepository $legacyRepository,
                                AuditoriumRepository $auditoriumRepository,
                                PeopleRepository $peopleRepository,
                                OrderMainRepository $orderRepository,
                                RegulationRepository $regulationRepository,
                                LocalResponsibilityService $service,
                                PeopleStampService $peopleStampService,
                                $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->responsibilityRepository = $responsibilityRepository;
        $this->legacyRepository = $legacyRepository;
        $this->auditoriumRepository = $auditoriumRepository;
        $this->peopleRepository = $peopleRepository;
        $this->orderRepository = $orderRepository;
        $this->regulationRepository = $regulationRepository;
        $this->service = $service;
        $this->peopleStampService = $peopleStampService;
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
     * Lists all LocalResponsibility models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchLocalResponsibility();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = ButtonsFormatter::primaryCreateLink('ответственность');
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml
        ]);
    }

    /**
     * Displays a single LocalResponsibility model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        /** @var LocalResponsibilityWork $responsible */
        $responsible = $this->responsibilityRepository->get($id);
        $history = $this->legacyRepository->getByResponsibility($responsible, BaseConsts::QUERY_ALL);

        return $this->render('view', [
            'model' => $responsible,
            'history' => $history
        ]);
    }

    /**
     * Creates a new LocalResponsibility model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ResponsibilityForm();
        $audsList = $this->auditoriumRepository->getAll();
        $peoples = $this->peopleRepository->getPeopleFromMainCompany();
        $orders = $this->orderRepository->getAll();
        $regulations = $this->regulationRepository->getAll();

        if ($model->load(Yii::$app->request->post())) {
            $model->getFilesInstances($model);
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $peopleStampId = $this->service->getPeopleStamps($model);
            if ($peopleStampId) {
                $modelResponsibility = LocalResponsibilityWork::fill(
                    $model->responsibilityType,
                    $model->branch,
                    $model->auditoriumId,
                    $model->quant,
                    $peopleStampId,
                    $model->regulationId,
                    $model->filesList
                );

                $modelLegacy = LegacyResponsibleWork::fill(
                    $peopleStampId,
                    $model->responsibilityType,
                    $model->branch,
                    $model->auditoriumId,
                    $model->quant,
                    $model->startDate,
                    $model->endDate,
                    $model->orderId
                );

                $this->responsibilityRepository->save($modelResponsibility);

                $this->service->saveFilesFromModel($modelResponsibility);
                $modelResponsibility->releaseEvents();

                $this->legacyRepository->save($modelLegacy);

                return $this->redirect(['view', 'id' => $modelResponsibility->id]);
            }
            else {
                Yii::$app->session->setFlash('danger', 'Не удалось прикрепить человека к ответственности');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'audsList' => $audsList,
            'peoples' => $peoples,
            'orders' => $orders,
            'regulations' => $regulations,
        ]);
    }

    /**
     * Updates an existing LocalResponsibility model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        /** @var LocalResponsibilityWork $modelResponsibility */
        /** @var LegacyResponsibleWork $modelLegacy */
        $modelResponsibility = $this->responsibilityRepository->get($id);
        $modelLegacy = $this->legacyRepository->getByResponsibility($modelResponsibility, BaseConsts::QUERY_ONE, ['people']);
        $audsList = $this->auditoriumRepository->getAll();
        $peoples = $this->peopleRepository->getPeopleFromMainCompany();
        $orders = $this->orderRepository->getAll();
        $regulations = $this->regulationRepository->getAll();

        $model = ResponsibilityForm::fillFromModels($modelResponsibility, $modelLegacy);

        if ($model->load(Yii::$app->request->post())) {
            $model->getFilesInstances($model);
            if ($model->isDetach()) {
                if ($model->endDate == "") {
                    Yii::$app->session->setFlash('danger', 'Невозможно открепить ответственность, т.к. не заполнено поле "Дата открепления ответственности"');
                }
                else {
                    $this->service->detachResponsibility($modelResponsibility, $model->endDate);
                }
            }
            else {
                $peopleStampId = $this->peopleStampService->createStampFromPeople($model->peopleStampId);
                $model->peopleStampId = $peopleStampId;

                $modelLegacy = LegacyResponsibleWork::fill(
                    $model->peopleStampId,
                    $model->responsibilityType,
                    $model->branch,
                    $model->auditoriumId,
                    $model->quant,
                    $model->startDate,
                    $model->endDate,
                    $model->orderId
                );

                $modelResponsibility->loadField(
                    $model->responsibilityType,
                    $model->branch,
                    $model->auditoriumId,
                    $model->quant,
                    $model->peopleStampId,
                    $model->regulationId,
                    $model->filesList
                );

                if ($modelLegacy->start_date == null) {
                    Yii::$app->session->setFlash('danger', 'Невозможно прикрепить ответственность, т.к. не заполнено поле "Дата прикрепления ответственности"');
                }
                else {
                    $this->responsibilityRepository->save($modelResponsibility);
                    $this->legacyRepository->save($modelLegacy);
                }

                $this->service->saveFilesFromModel($modelResponsibility);
            }

            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update', [
            'model' => $model,
            'audsList' => $audsList,
            'peoples' => $peoples,
            'orders' => $orders,
            'regulations' => $regulations,
            'modelResponsibility' => $modelResponsibility,
        ]);
    }

    /**
     * Deletes an existing LocalResponsibility model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        /** @var LocalResponsibilityWork $model */
        $model = $this->responsibilityRepository->get($id);
        $this->responsibilityRepository->delete($model);

        return $this->redirect(['index']);
    }

    public function actionGetAuditorium()
    {
        if ($id = Yii::$app->request->post('id')) {
            $operationPosts = count(Yii::$app->branches->getList());

            $result = HtmlBuilder::createEmptyOption();
            if ($operationPosts > 0) {
                $result .= HtmlBuilder::buildOptionList(
                    (Yii::createObject(AuditoriumRepository::class))->getByBranch($id)
                );
            }
        }

        echo $result;
    }

    /**
     * Finds the LocalResponsibility model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LocalResponsibilityWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LocalResponsibilityWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
