<?php

namespace frontend\controllers\order;

use app\models\forms\OrderMainForm;
use common\components\traits\AccessControl;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\helpers\ButtonsFormatter;
use common\helpers\ErrorAssociationHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\DocumentOrder;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\expire\ExpireRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\general\UserRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\order\OrderMainRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\models\forms\ExpireForm;
use frontend\models\search\SearchOrderMain;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderMainWork;
use frontend\services\order\DocumentOrderService;
use frontend\services\order\OrderMainService;
use frontend\services\order\OrderPeopleService;
use yii;
use yii\helpers\ArrayHelper;

class OrderMainController extends DocumentController
{
    use AccessControl;

    private OrderMainRepository $repository;
    private DocumentOrderRepository $documentOrderRepository;
    private OrderMainService $service;
    public DocumentOrderService $documentOrderService;
    private ExpireRepository $expireRepository;
    private OrderPeopleRepository $orderPeopleRepository;
    private UserRepository $userRepository;
    private RegulationRepository $regulationRepository;
    private LockWizard $lockWizard;
    private OrderPeopleService $orderPeopleService;
    private PeopleRepository $peopleRepository;

    public function __construct(
        $id,
        $module,
        OrderMainRepository $repository,
        DocumentOrderRepository $documentOrderRepository,
        OrderMainService $service,
        DocumentOrderService $documentOrderService,
        ExpireRepository $expireRepository,
        OrderPeopleRepository $orderPeopleRepository,
        UserRepository $userRepository,
        RegulationRepository $regulationRepository,
        LockWizard $lockWizard,
        OrderPeopleService $orderPeopleService,
        PeopleRepository $peopleRepository,
        $config = []
    )
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->service = $service;
        $this->documentOrderService = $documentOrderService;
        $this->documentOrderRepository = $documentOrderRepository;
        $this->expireRepository = $expireRepository;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->userRepository = $userRepository;
        $this->regulationRepository = $regulationRepository;
        $this->lockWizard = $lockWizard;
        $this->repository = $repository;
        $this->orderPeopleService = $orderPeopleService;
        $this->peopleRepository = $peopleRepository;

    }
    public function actionIndex(){
        $searchModel = new SearchOrderMain();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = array_merge(
            ButtonsFormatter::primaryCreateLink('приказ'),
            ButtonsFormatter::anyOneLink('Добавить резерв', Yii::$app->frontUrls::ORDER_MAIN_RESERVE, ButtonsFormatter::BTN_SUCCESS),
        );
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml
        ]);
    }

    public function actionReserve()
    {
        $model = new OrderMainWork();
        $this->service->createReserve($model);
        $this->repository->save($model);
        return $this->redirect(['index']);
    }

    public function actionCreate(){

        $form = new OrderMainForm(
            new OrderMainWork(),
            $this->peopleRepository->getOrderedList(),
            $this->documentOrderRepository->getAllByType(DocumentOrderWork::ORDER_MAIN),
            $this->regulationRepository->getOrderedList(),
            [new ExpireForm()],
            NULL,
            NULL
        );


        $post = Yii::$app->request->post();
        if ($form->entity->load($post)) {
            $this->documentOrderService->getPeopleStamps($form->entity);
            if (!$form->entity->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($form->entity->getErrors()));
            }

            $form->entity->generateOrderNumber();
            $this->repository->save($form->entity);
            $this->documentOrderService->getFilesInstances($form->entity);
            $this->service->addExpireEvent($post["ExpireForm"], $form->entity);
            $this->orderPeopleService->addOrderPeopleEvent($post["OrderMainWork"]["responsible_id"], $form->entity);
            $this->documentOrderService->saveFilesFromModel($form->entity);
            $form->entity->releaseEvents();
            $form->entity->checkModel(ErrorAssociationHelper::getOrderMainErrorsList(), DocumentOrderWork::tableName(), $form->entity->id);
            return $this->redirect(['view', 'id' => $form->entity->id]);
        }
        return $this->render('create', [
            'model' => $form->entity,
            'people' => $form->people,
            'modelExpire' => $form->modelExpire,
            'orders' => $form->orders,
            'regulations' => $form->regulations
        ]);
    }
    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, DocumentOrder::tableName(), Yii::$app->user->id)) {
            /* @var OrderMainWork $model */
            $form = new OrderMainForm(
                $this->repository->get($id),
                $this->peopleRepository->getOrderedList(),
                $this->documentOrderRepository->getExceptByIdAndStatus($id, DocumentOrderWork::ORDER_MAIN),
                $this->regulationRepository->getOrderedList(),
                [new ExpireForm()],
                $this->service->getChangedDocumentsTable($id),
                $this->documentOrderService->getUploadedFilesTables($this->repository->get($id))
            );
            $form->entity->setValuesForUpdate();
            $post = Yii::$app->request->post();
            $this->documentOrderService->setResponsiblePeople(ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'), $form->entity);
            if ($form->entity->load($post)) {
                $this->lockWizard->unlockObject($id, DocumentOrder::tableName());
                $this->documentOrderService->getPeopleStamps($form->entity);
                if (!$form->entity->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($form->entity->getErrors()));
                }
                $this->repository->save($form->entity);
                $this->documentOrderService->getFilesInstances($form->entity);
                $this->orderPeopleService->updateOrderPeopleEvent(ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'),
                    $post["OrderMainWork"]["responsible_id"], $form->entity);
                $this->service->addExpireEvent($post["ExpireForm"], $form->entity);
                $this->documentOrderService->saveFilesFromModel($form->entity);
                $form->entity->releaseEvents();
                $form->entity->checkModel(ErrorAssociationHelper::getOrderMainErrorsList(), DocumentOrderWork::tableName(), $form->entity->id);
                return $this->redirect(['view', 'id' => $form->entity->id]);
            }
            return $this->render('update', [
                'orders' => $form->orders,
                'model' => $form->entity,
                'people' => $form->people,
                'modelExpire' => $form->modelExpire,
                'regulations' => $form->regulations,
                'modelChangedDocuments' => $form->modelChangedDocuments,
                'scanFile' => $form->tables['scan'],
                'docFiles' => $form->tables['docs'],
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, DocumentOrder::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionDelete($id)
    {
        $model = $this->documentOrderRepository->get($id);
        $this->documentOrderService->documentOrderDelete($model);
        $model->releaseEvents();
        return $this->redirect(['index']);
    }

    public function actionView($id)
    {
        $links = ButtonsFormatter::generateOrderLinks($id);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        $modelResponsiblePeople = implode('<br>',
            $this->documentOrderService->createOrderPeopleArray(
                $this->orderPeopleRepository->getResponsiblePeople($id)
            )
        );
        $modelChangedDocuments = implode('<br>',
            $this->service->createChangedDocumentsArray(
                $this->expireRepository->getExpireByActiveRegulationId($id)
            )
        );

        /** @var OrderMainWork $model */
        $model = $this->repository->get($id);
        $model->checkFilesExist();

        return $this->render('view', [
            'model' => $model,
            'modelResponsiblePeople' => $modelResponsiblePeople,
            'modelChangedDocuments' => $modelChangedDocuments,
            'buttonsAct' => $buttonHtml
        ]);
    }

    public function actionDeleteDocument($id, $modelId)
    {
        $this->expireRepository->deleteByActiveRegulationId($id);
        return $this->redirect(['update', 'id' => $modelId]);
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