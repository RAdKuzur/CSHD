<?php

namespace frontend\controllers\order;

use app\components\DynamicWidget;
use app\events\document_order\DocumentOrderDeleteEvent;
use app\models\forms\OrderTrainingForm;
use common\components\traits\AccessControl;
use common\helpers\ButtonsFormatter;
use common\helpers\html\HtmlBuilder;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\order\DocumentOrderRepository;
use frontend\components\GroupParticipantWidget;
use frontend\invokables\OrderLoader;
use frontend\models\search\SearchOrderTraining;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderTrainingWork;
use frontend\services\order\DocumentOrderService;
use frontend\services\order\OrderPeopleService;
use frontend\services\order\OrderTrainingService;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\general\PeopleStampRepository;
use common\repositories\order\OrderTrainingRepository;
use common\services\general\files\FileService;
use DomainException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class OrderTrainingController extends DocumentController
{
    use AccessControl;

    private PeopleStampRepository $peopleStampRepository;
    private DocumentOrderService $documentOrderService;
    private OrderTrainingService $orderTrainingService;
    private OrderPeopleRepository $orderPeopleRepository;
    private OrderPeopleService $orderPeopleService;
    private OrderTrainingRepository $orderTrainingRepository;
    private TrainingGroupRepository $trainingGroupRepository;
    private LockWizard $lockWizard;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    private PeopleRepository $peopleRepository;
    private DocumentOrderRepository $documentOrderRepository;

    public function __construct(
        $id,
        $module,
        DocumentOrderService $documentOrderService,
        OrderTrainingService $orderTrainingService,
        OrderPeopleRepository $orderPeopleRepository,
        OrderPeopleService $orderPeopleService,
        OrderTrainingRepository $orderTrainingRepository,
        TrainingGroupRepository $trainingGroupRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        PeopleRepository $peopleRepository,
        LockWizard $lockWizard,
        DocumentOrderRepository $documentOrderRepository,
        FileService $fileService,
        FilesRepository $filesRepository,
        $config = []
    )
    {
        $this->documentOrderService = $documentOrderService;
        $this->orderTrainingService = $orderTrainingService;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->orderPeopleService = $orderPeopleService;
        $this->orderTrainingRepository = $orderTrainingRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->peopleRepository = $peopleRepository;
        $this->lockWizard = $lockWizard;
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
        $this->documentOrderRepository = $documentOrderRepository;
        parent::__construct($id, $module, $fileService, $filesRepository, $config);
    }

    public function actionIndex()
    {
        $searchModel = new SearchOrderTraining();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = ButtonsFormatter::primaryCreateLink('приказ');
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml
        ]);
    }

    public function actionView($id, $error = NULL)
    {
        $links = ButtonsFormatter::generateOrderLinks($id);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        /** @var OrderTrainingWork $model */
        $model = $this->orderTrainingRepository->get($id);
        $modelResponsiblePeople = implode('<br>',
            $this->documentOrderService->createOrderPeopleArray(
                $this->orderPeopleRepository->getResponsiblePeople($id)
            )
        );
        $status = $this->orderTrainingService->getStatus($model);
        $groups = $this->orderTrainingService->getGroupTable($model) ;
        $participants = $this->orderTrainingService->getGroupParticipantTable($model, $status);

        $model->checkFilesExist();

        return $this->render('view', [
            'model' => $model,
            'modelResponsiblePeople' => $modelResponsiblePeople,
            'groups' => $groups,
            'participants' => $participants,
            'error' => $error,
            'buttonsAct' => $buttonHtml
        ]);
    }

    public function actionCreate()
    {
        $model = new OrderTrainingWork();
        $form = new OrderTrainingForm(
            $this->peopleRepository->getOrderedList(),
            $this->orderTrainingService->getGroupsEmptyDataProvider(),
            $this->orderTrainingService->getParticipantEmptyDataProvider(),
            NULL,
            NULL,
            NULL,
            NULL,
        );
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $this->documentOrderService->getPeopleStamps($model);
            if (!$model->validate()) {
               throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $respPeopleId = DynamicWidget::getData(basename(OrderTrainingWork::class), "responsible_id", $post);
            $this->documentOrderService->getFilesInstances($model);
            $model->generateOrderNumber();
            $this->orderTrainingRepository->save($model);
            $status = $this->orderTrainingService->getStatus($model);
            $error = $this->orderTrainingService->createOrderTrainingGroupParticipantEvent($model, $status, $post);
            $this->documentOrderService->saveFilesFromModel($model);
            $this->orderPeopleService->addOrderPeopleEvent($respPeopleId, $model);
            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->id, 'error' => $error]);
        }
        return $this->render('create', [
            'model' => $model,
            'people' => $form->people,
            'groups' => $form->groups,
            'groupParticipant' => $form->groupParticipant,
            'groupCheckOption' => [],
            'groupParticipantOption' => [],

        ]);
    }

    public function actionUpdate($id, $error = NULL)
    {
        if ($this->lockWizard->lockObject($id, DocumentOrderWork::tableName(), Yii::$app->user->id)) {
            $model = $this->orderTrainingRepository->get($id);
            $this->orderTrainingService->setBranch($model);
            $status = $this->orderTrainingService->getStatus($model);
            $number = $model->order_number;
            $model->setValuesForUpdate();
            $post = Yii::$app->request->post();
            $form = new OrderTrainingForm(
                $this->peopleRepository->getOrderedList(),
                $this->orderTrainingService->getGroupsDataProvider($model),
                $this->orderTrainingService->getParticipantsDataProvider($model),
                $this->trainingGroupRepository->getByBranchQuery($model->branch)->all(),
                $this->documentOrderService->getUploadedFilesTables($model),
                $this->trainingGroupRepository->getAttachedGroupsByOrder($id, $status),
                $this->trainingGroupParticipantRepository->getAttachedParticipantByOrder($id, $status)
            );

            $this->documentOrderService->setResponsiblePeople(ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'), $model);
            if ($model->load($post)) {
                $this->lockWizard->unlockObject($id, DocumentOrderWork::tableName());
                $this->documentOrderService->getPeopleStamps($model);
                if (!$model->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
                }

                $this->documentOrderService->getFilesInstances($model);
                $model->order_number = $number;
                $this->orderTrainingRepository->save($model);
                $error = $this->orderTrainingService->updateOrderTrainingGroupParticipantEvent($model, $status, $post);
                if($error) {
                    return $this->redirect(['update', 'id' => $id, 'error' => $error]);
                }
                $this->documentOrderService->saveFilesFromModel($model);
                $this->orderPeopleService->updateOrderPeopleEvent(
                    ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'),
                    $post["OrderTrainingWork"]["responsible_id"], $model);
                $model->releaseEvents();
                return $this->redirect(['view', 'id' => $model->id]);
            }
            return $this->render('update', [
                'model' => $model,
                'people' => $form->people,
                'groups' => $form->groups,
                'groupParticipant' => $form->groupParticipant,
                'transferGroups' => $form->transferGroups,
                'scanFile' => $form->tables['scan'],
                'docFiles' => $form->tables['docs'],
                'groupCheckOption' => $form->groupCheckOption,
                'groupParticipantOption' => $form->groupParticipantOption,
                'error' => $error
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, DocumentOrderWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }
    public function actionDelete($id){
        $model = $this->documentOrderRepository->get($id);
        $this->documentOrderService->documentOrderDelete($model);
        if ($this->orderTrainingService->isPossibleToDeleteOrder($model->id)) {
            $model->releaseEvents();
            return $this->redirect(['index']);
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }
    public function actionGetListByBranch()
    {
        $branchId = Yii::$app->request->get('branch_id');
        $nomenclatureList = Yii::$app->nomenclature->getListByBranch($branchId); // Получаем список по номеру отдела
        return $this->asJson($nomenclatureList); // Возвращаем список в формате JSON
    }
    public function actionSetNameOrder()
    {
        $nomenclature = Yii::$app->request->get('nomenclature');
        $status = NomenclatureDictionary::getStatus($nomenclature);
        return NomenclatureDictionary::getOrderName($status);
    }
    public function actionGetGroupByBranch($branch)
    {
        $groupCheckOption = json_decode(Yii::$app->request->get('groupCheckOption'));
        $modelId = Yii::$app->request->get('modelId');
        $groupsQuery = $this->trainingGroupRepository->getByBranchQuery($branch);
        $dataProvider = new ActiveDataProvider([
            'query' => $groupsQuery,
        ]);
        return $this->asJson([
            'gridHtml' => $this->renderPartial(GroupParticipantWidget::GROUP_VIEW, [
                'dataProvider' => $dataProvider,
                'model' => $this->orderTrainingRepository->get($modelId),
                'groupCheckOption' => $groupCheckOption,
            ]),
        ]);
    }
    public function actionGenerateOrder($id)
    {
        $model = $this->documentOrderRepository->get($id);
        $loader = new OrderLoader(
            $this->documentOrderService->generateOrder($model),
            "Приказ №" . $model->order_number
        );
        $loader();
    }
    public function actionGetGroupParticipantsByBranch()
    {
        $groupIds = Yii::$app->request->get('groupIds');
        $modelId = Yii::$app->request->get('modelId');
        $groupIds = json_decode($groupIds);
        if ($modelId == 0){
            $groupCheckOption = [];
            $groupParticipantOption = [];
            $nomenclature = Yii::$app->request->get('nomenclature');
            $status = NomenclatureDictionary::getStatus($nomenclature);
            //create
            if ($status == NomenclatureDictionary::ORDER_ENROLL){
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantsToEnrollCreate($groupIds)
                ]);
            }
            if ($status == NomenclatureDictionary::ORDER_DEDUCT){
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantsToDeductCreate($groupIds)
                ]);
            }
            if ($status == NomenclatureDictionary::ORDER_TRANSFER){
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantsToTransferCreate($groupIds)
                ]);
            }
        }
        else {
            //update


            $model = $this->orderTrainingRepository->get($modelId);
            $status = $this->orderTrainingService->getStatus($model);

            $nomenclature = $model->getNomenclature();
            if ($status == NomenclatureDictionary::ORDER_ENROLL){
                $groupCheckOption = $this->trainingGroupRepository->getAttachedGroupsByOrder($modelId,  $status);
                $groupParticipantOption = $this->trainingGroupParticipantRepository->getAttachedParticipantByOrder($modelId, $status);
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantToEnrollUpdate($groupIds, $modelId)
                ]);
            }
            else if ($status == NomenclatureDictionary::ORDER_DEDUCT) {
                $groupCheckOption = $this->trainingGroupRepository->getAttachedGroupsByOrder($modelId,  $status);
                $groupParticipantOption = $this->trainingGroupParticipantRepository->getAttachedParticipantByOrder($modelId, $status);
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantToDeductUpdate($groupIds, $modelId)
                ]);
            }
            else if ($status == NomenclatureDictionary::ORDER_TRANSFER){
                $groupCheckOption = $this->trainingGroupRepository->getAttachedGroupsByOrder($modelId,  $status);
                $groupParticipantOption = $this->trainingGroupParticipantRepository->getAttachedParticipantByOrder($modelId, $status);
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantToTransferUpdate($groupIds, $modelId)
                ]);
            }
            else {
                $groupCheckOption = [];
            }
        }
        return $this->asJson([
            'gridHtml' => $this->renderPartial(GroupParticipantWidget::GROUP_PARTICIPANT_VIEW, [
                'dataProvider' => $dataProvider,
                'model' => $this->orderTrainingRepository->get($modelId),
                'nomenclature' => $nomenclature,
                'transferGroups' => $this->trainingGroupRepository->getQueryById($groupIds)->all(),
                'groupCheckOption' => $groupCheckOption,
                'groupParticipantOption' => $groupParticipantOption,
            ]),
        ]);
    }
    public function actionSetPreamble()
    {
        $nomenclature = Yii::$app->request->get('nomenclature');
        $status = NomenclatureDictionary::getStatus($nomenclature);
        return $status;
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