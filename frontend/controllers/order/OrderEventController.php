<?php

namespace frontend\controllers\order;

use app\components\DynamicWidget;
use app\events\act_participant\ActParticipantBranchDeleteEvent;
use app\events\act_participant\ActParticipantDeleteEvent;
use app\events\act_participant\SquadParticipantDeleteByIdEvent;
use app\events\document_order\DocumentOrderDeleteEvent;
use app\models\forms\OrderEventBuilderForm;
use app\models\work\order\OrderEventGenerateWork;
use app\services\order\OrderEventGenerateService;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\components\traits\AccessControl;
use common\helpers\ButtonsFormatter;
use common\helpers\ErrorAssociationHelper;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\OrderEventGenerate;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\order\OrderEventGenerateRepository;
use frontend\events\general\FileDeleteEvent;
use frontend\invokables\OrderLoader;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\general\FilesWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderEventWork;
use frontend\models\work\team\ActParticipantWork;
use frontend\services\act_participant\ActParticipantService;
use frontend\services\event\OrderEventFormService;
use frontend\services\order\DocumentOrderService;

use frontend\services\order\OrderPeopleService;
use frontend\services\team\TeamService;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\dictionaries\CompanyRepository;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\general\PeopleStampRepository;
use common\repositories\order\OrderEventRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\facades\ActParticipantFacade;
use frontend\forms\OrderEventForm;
use frontend\models\forms\ActParticipantForm;
use frontend\models\search\SearchOrderEvent;
use frontend\services\event\ForeignEventService;
use Yii;
use yii\helpers\ArrayHelper;

class OrderEventController extends DocumentController
{
    use AccessControl;

    private OrderPeopleService $orderPeopleService;
    private DocumentOrderService $documentOrderService;
    private PeopleRepository $peopleRepository;
    private OrderEventRepository $orderEventRepository;
    private OrderPeopleRepository $orderPeopleRepository;
    private ForeignEventRepository $foreignEventRepository;
    private OrderEventFormService $orderEventFormService;
    private ForeignEventService $foreignEventService;
    private ActParticipantService $actParticipantService;
    private ActParticipantRepository $actParticipantRepository;
    private ActParticipantFacade $actParticipantFacade;
    private ForeignEventParticipantsRepository $foreignEventParticipantsRepository;
    private CompanyRepository $companyRepository;
    private LockWizard $lockWizard;
    private OrderEventGenerateRepository $orderEventGenerateRepository;
    private OrderEventGenerateService $orderEventGenerateService;
    private TeamService $teamService;
    private DocumentOrderRepository $documentOrderRepository;
    private FilesRepository $filesRepository;

    public function __construct(
        $id, $module,
        OrderPeopleService $orderPeopleService,
        DocumentOrderService $documentOrderService,
        PeopleRepository $peopleRepository,
        OrderEventRepository $orderEventRepository,
        OrderPeopleRepository $orderPeopleRepository,
        ForeignEventRepository $foreignEventRepository,
        OrderEventFormService $orderEventFormService,
        ForeignEventService $foreignEventService,
        ActParticipantService $actParticipantService,
        ActParticipantRepository $actParticipantRepository,
        FileService $fileService,
        FilesRepository $fileRepository,
        ActParticipantFacade $actParticipantFacade,
        TeamService $teamService,
        ForeignEventParticipantsRepository $foreignEventParticipantsRepository,
        CompanyRepository $companyRepository,
        LockWizard $lockWizard,
        OrderEventGenerateRepository $orderEventGenerateRepository,
        OrderEventGenerateService $orderEventGenerateService,
        DocumentOrderRepository $documentOrderRepository,
        FilesRepository $filesRepository,
        $config = []
    )
    {
        $this->orderPeopleService = $orderPeopleService;
        $this->documentOrderService = $documentOrderService;
        $this->peopleRepository = $peopleRepository;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->orderEventRepository = $orderEventRepository;
        $this->orderEventFormService = $orderEventFormService;
        $this->foreignEventService = $foreignEventService;
        $this->actParticipantService = $actParticipantService;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->actParticipantFacade = $actParticipantFacade;
        $this->foreignEventParticipantsRepository = $foreignEventParticipantsRepository;
        $this->companyRepository = $companyRepository;
        $this->lockWizard = $lockWizard;
        $this->orderEventGenerateRepository = $orderEventGenerateRepository;
        $this->orderEventGenerateService = $orderEventGenerateService;
        $this->teamService = $teamService;
        $this->documentOrderRepository = $documentOrderRepository;
        $this->filesRepository = $fileRepository;
        parent::__construct($id, $module, $fileService, $fileRepository, $config);
    }
    public function actionIndex() {
        $searchModel = new SearchOrderEvent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = ButtonsFormatter::primaryCreateLink('приказ');
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml
        ]);
    }

    public function actionCreate() {
        $form = new OrderEventBuilderForm(
            new OrderEventForm(),
            $this->peopleRepository->getOrderedList(),
            [new ActParticipantForm],
            [],
            [],
            $this->foreignEventParticipantsRepository->getSortedList(),
            $this->companyRepository->getList(),
            NULL,
            NULL
        );
        $post = Yii::$app->request->post();
        if($form->orderEventForm->load($post)) {
            $acts = $post["ActParticipantForm"];
            if (!$form->orderEventForm->validate()) {
                  throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($form->orderEventForm->getErrors()));
            }
            $this->orderEventFormService->getFilesInstances($form->orderEventForm);
            $respPeopleId = DynamicWidget::getData(basename(OrderEventForm::class), "responsible_id", $post);
            $modelOrderEvent = OrderEventWork::fill(
                $form->orderEventForm->order_copy_id,
                NomenclatureDictionary::ADMIN_ORDER,
                $form->orderEventForm->order_postfix,
                $form->orderEventForm->order_date,
                $form->orderEventForm->order_name,
                $form->orderEventForm->signed_id,
                $form->orderEventForm->bring_id,
                $form->orderEventForm->executor_id,
                $form->orderEventForm->key_words,
                $form->orderEventForm->creator_id,
                $form->orderEventForm->last_edit_id,
                $form->orderEventForm->target,
                DocumentOrderWork::ORDER_EVENT, //$model->type,
                $form->orderEventForm->state,
                $form->orderEventForm->nomenclature_id,
                $form->orderEventForm->study_type,
                $form->orderEventForm->scanFile,
                $form->orderEventForm->docFiles,
            );
            $modelOrderEvent->generateOrderNumber();
            $this->documentOrderService->getPeopleStamps($modelOrderEvent);
            $number = $modelOrderEvent->getNumberPostfix();
            $this->orderEventRepository->save($modelOrderEvent);
            $generateInfo = OrderEventGenerateWork::fill(
                $modelOrderEvent->id,
                $form->orderEventForm->purpose,
                $form->orderEventForm->docEvent,
                $form->orderEventForm->respPeopleInfo,
                $form->orderEventForm->timeProvisionDay,
                $form->orderEventForm->extraRespInsert,
                $form->orderEventForm->timeInsertDay,
                $form->orderEventForm->extraRespMethod,
                $form->orderEventForm->extraRespInfoStuff,
                $form->orderEventForm->documentDetails
            );
            $this->orderEventGenerateService->setPeopleStamp($generateInfo);
            $this->orderEventGenerateRepository->save($generateInfo);
            $this->documentOrderService->saveFilesFromModel($modelOrderEvent);
            $modelForeignEvent = ForeignEventWork::fill(
                $form->orderEventForm->eventName,
                $form->orderEventForm->organizer_id,
                $form->orderEventForm->dateBegin,
                $form->orderEventForm->dateEnd,
                $form->orderEventForm->city,
                $form->orderEventForm->eventWay,
                $form->orderEventForm->eventLevel,
                $form->orderEventForm->minister,
                $form->orderEventForm->minAge,
                $form->orderEventForm->maxAge,
                $form->orderEventForm->keyEventWords,
                $modelOrderEvent->id,
                $form->orderEventForm->actFiles
            );
            $this->foreignEventRepository->save($modelForeignEvent);
            $modelForeignEvent->checkModel(ErrorAssociationHelper::getForeignEventErrorsList(), ForeignEventWork::tableName(), $modelForeignEvent->id);

            $this->orderPeopleService->addOrderPeopleEvent($respPeopleId, $modelOrderEvent);
            $this->foreignEventService->saveActFilesFromModel($modelForeignEvent, $form->orderEventForm->actFiles, $number);
            $form->orderEventForm->releaseEvents();
            $modelForeignEvent->releaseEvents();
            $modelOrderEvent->releaseEvents();
            $this->actParticipantService->addActParticipant($acts, $modelForeignEvent->id);
            return $this->redirect(['view', 'id' => $modelOrderEvent->id]);
        }
        return $this->render('create', [
            'model' => $form->orderEventForm,
            'people' => $form->people,
            'modelActs' => $form->modelActs,
            'nominations' => $form->nominations,
            'teams' => $form->teams,
            'participants' => $form->participants,
            'company' => $form->company
        ]);
    }
    public function actionView($id)
    {
        $links = ButtonsFormatter::generateOrderLinks($id);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        /* @var OrderEventWork $modelOrderEvent */
        /* @var ForeignEventWork $foreignEvent */
        $modelResponsiblePeople = implode('<br>',
            $this->documentOrderService->createOrderPeopleArray(
                $this->orderPeopleRepository->getResponsiblePeople($id)
            )
        );
        $modelOrderEvent = $this->orderEventRepository->get($id);
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($modelOrderEvent->id);
        $actTable = $this->actParticipantService->createActTable($foreignEvent->id);
        return $this->render('view',
            [
                'model' => $modelOrderEvent,
                'foreignEvent' => $foreignEvent,
                'modelResponsiblePeople' => $modelResponsiblePeople,
                'actTable' => $actTable,
                'buttonsAct' => $buttonHtml
            ]
        );
    }
    public function actionUpdate($id)
    {
        /* @var $modelOrderEvent OrderEventWork */
        if ($this->lockWizard->lockObject($id, DocumentOrderWork::tableName(), Yii::$app->user->id)) {
            /* @var OrderEventWork $modelOrderEvent */
            /* @var ForeignEventWork $modelForeignEvent */
            $modelOrderEvent = $this->orderEventRepository->get($id);
            $modelForeignEvent = $this->foreignEventRepository->getByDocOrderId($modelOrderEvent->id);
            $form = new OrderEventBuilderForm(
                OrderEventForm::fill($modelOrderEvent, $modelForeignEvent),
                $this->peopleRepository->getOrderedList(),
                [new ActParticipantForm],
                $this->teamService->getNamesByForeignEventId($modelForeignEvent->id),
                array_unique(ArrayHelper::getColumn($this->actParticipantRepository->getByForeignEventIds([$modelForeignEvent->id]), 'nomination')),
                $this->foreignEventParticipantsRepository->getSortedList(),
                $this->companyRepository->getList(),
                $this->actParticipantService->createActTable($modelForeignEvent->id),
                $this->documentOrderService->getUploadedFilesTables($modelOrderEvent),
            );

            $form->orderEventForm->fillExtraInfo($this->orderEventGenerateRepository->getByOrderId($id));
            $this->documentOrderService->setResponsiblePeople(ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'), $form->orderEventForm);
            $orderNumber = $form->orderEventForm->order_number;
            $form->orderEventForm->setValuesForUpdate();
            $post = Yii::$app->request->post();
            if ($form->orderEventForm->load($post)) {
                $this->lockWizard->unlockObject($id, DocumentOrderWork::tableName());
                if (!$form->orderEventForm->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($form->orderEventForm->getErrors()));
                }
                $acts = $post["ActParticipantForm"];
                $this->orderEventFormService->getFilesInstances($form->orderEventForm);
                $modelOrderEvent->fillUpdate(
                    $form->orderEventForm->order_copy_id,
                    $orderNumber,
                    $form->orderEventForm->order_postfix,
                    $form->orderEventForm->order_date,
                    $form->orderEventForm->order_name,
                    $form->orderEventForm->signed_id,
                    $form->orderEventForm->bring_id,
                    $form->orderEventForm->executor_id,
                    $form->orderEventForm->key_words,
                    $form->orderEventForm->creator_id,
                    $form->orderEventForm->last_edit_id,
                    $form->orderEventForm->target,
                    DocumentOrderWork::ORDER_EVENT, //$model->type,
                    $form->orderEventForm->state,
                    $form->orderEventForm->nomenclature_id,
                    $form->orderEventForm->study_type,
                    $form->orderEventForm->scanFile,
                    $form->orderEventForm->docFiles,
                );
                $this->documentOrderService->getPeopleStamps($modelOrderEvent);
                $this->orderEventRepository->save($modelOrderEvent);
                $generateInfo = $this->orderEventGenerateRepository->getByOrderId($id);
                if ($this->orderEventGenerateRepository->existsByOrderId($id)) {
                    $generateInfo->fillUpdate(
                        $modelOrderEvent->id,
                        $form->orderEventForm->purpose,
                        $form->orderEventForm->docEvent,
                        $form->orderEventForm->respPeopleInfo,
                        $form->orderEventForm->timeProvisionDay,
                        $form->orderEventForm->extraRespInsert,
                        $form->orderEventForm->timeInsertDay,
                        $form->orderEventForm->extraRespMethod,
                        $form->orderEventForm->extraRespInfoStuff,
                        $form->orderEventForm->documentDetails
                    );
                }
                else {
                    $generateInfo = OrderEventGenerateWork::fill(
                        $modelOrderEvent->id,
                        $form->orderEventForm->purpose,
                        $form->orderEventForm->docEvent,
                        $form->orderEventForm->respPeopleInfo,
                        $form->orderEventForm->timeProvisionDay,
                        $form->orderEventForm->extraRespInsert,
                        $form->orderEventForm->timeInsertDay,
                        $form->orderEventForm->extraRespMethod,
                        $form->orderEventForm->extraRespInfoStuff,
                        $form->orderEventForm->documentDetails
                    );
                }
                $this->orderEventGenerateService->setPeopleStamp($generateInfo);
                $this->orderEventGenerateRepository->save($generateInfo);
                $this->documentOrderService->saveFilesFromModel($modelOrderEvent);
                $this->orderPeopleService->updateOrderPeopleEvent(
                    ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'),
                    $post["OrderEventForm"]["responsible_id"], $modelOrderEvent);
                if($this->foreignEventRepository->existByOrderId($id)) {
                    $modelForeignEvent->fillUpdate(
                        $form->orderEventForm->eventName,
                        $form->orderEventForm->organizer_id,
                        $form->orderEventForm->dateBegin,
                        $form->orderEventForm->dateEnd,
                        $form->orderEventForm->city,
                        $form->orderEventForm->eventWay,
                        $form->orderEventForm->eventLevel,
                        $form->orderEventForm->minister,
                        $form->orderEventForm->minAge,
                        $form->orderEventForm->maxAge,
                        $form->orderEventForm->keyEventWords,
                        $modelOrderEvent->id,
                        $form->orderEventForm->actFiles
                    );
                }
                else {
                    $modelForeignEvent = ForeignEventWork::fill(
                        $form->orderEventForm->eventName,
                        $form->orderEventForm->organizer_id,
                        $form->orderEventForm->dateBegin,
                        $form->orderEventForm->dateEnd,
                        $form->orderEventForm->city,
                        $form->orderEventForm->eventWay,
                        $form->orderEventForm->eventLevel,
                        $form->orderEventForm->minister,
                        $form->orderEventForm->minAge,
                        $form->orderEventForm->maxAge,
                        $form->orderEventForm->keyEventWords,
                        $modelOrderEvent->id,
                        $form->orderEventForm->actFiles
                    );
                }
                $this->foreignEventRepository->save($modelForeignEvent);
                $modelForeignEvent->checkModel(ErrorAssociationHelper::getForeignEventErrorsList(), ForeignEventWork::tableName(), $modelForeignEvent->id);
                $this->actParticipantService->addActParticipant($acts, $modelForeignEvent->id);
                $modelOrderEvent->releaseEvents();
                return $this->redirect(['view', 'id' => $modelOrderEvent->id]);
            }
            return $this->render('update', [
                'model' => $form->orderEventForm,
                'people' => $form->people,
                'scanFile' => $form->tables['scan'],
                'docFiles' => $form->tables['docs'],
                'nominations' => $form->nominations,
                'teams' => $form->teams,
                'modelActs' => $form->modelActs,
                'actTable' => $form->actTable,
                'participants' => $form->participants,
                'company' => $form->company,
                'id' => $id
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, DocumentOrderWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }
    public function actionAct($id)
    {
        /* @var $act ActParticipantWork */
        $act = [$this->actParticipantRepository->get($id)];
        $foreignEventId = $act[0]->foreign_event_id;
        $orderId = ($this->foreignEventRepository->get($foreignEventId))->order_participant_id;
        if($act[0] == NULL){
            return $this->redirect(['index']);
        }
        $this->actParticipantService->getPeopleStamp($act[0]);
        $data = $this->actParticipantFacade->prepareActFacade($act);
        $modelAct = $data['modelAct'];
        $people = $data['people'];
        $nominations = $data['nominations'];
        $teams = $data['teams'];
        $defaultTeam = $data['defaultTeam'];
        $tables = $data['tables'];
        $participants = $data['participants'];
        $post = Yii::$app->request->post();
        if($post != NULL){
            $post = $post["ActParticipantForm"];
            $act[0]->fillUpdate(
                $post[0]["firstTeacher"],
                $post[0]["secondTeacher"],
                $act[0]->team_name_id,
                $act[0]->foreign_event_id,
                $act[0]->focus,
                $act[0]->type,
                NULL,
                $act[0]->nomination,
                $act[0]->form
            );
            $this->actParticipantService->setPeopleStamp($act[0]);
            $this->actParticipantRepository->save($act[0]);
            $this->actParticipantService->getFilesInstance($modelAct[0], 0);
            $act[0]->actFiles = $modelAct[0]->actFiles;
            $this->actParticipantService->saveFilesFromModel($act[0], 0);
            //при замене select в act-update заменить в следующей строчке $post[0]["participant"] на что-то другое
            $this->actParticipantService->updateSquadParticipant($act[0], $post[0]["participant"]);
            return $this->redirect(['view', 'id' => $orderId]);
        }
        return $this->render('act-update', [
            'act' => $act[0],
            'modelActs' => $modelAct,
            'people' => $people,
            'nominations' => $nominations,
            'teams' => $teams,
            'defaultTeam' => $defaultTeam['name'],
            'tables' => $tables,
            'participants' => $participants,
            'orderId' => $orderId,
        ]);
    }
    public function actionDeletePeople($id, $modelId)
    {
        $this->orderPeopleRepository->deleteByPeopleId($id);
        return $this->redirect(['update', 'id' => $modelId]);
    }
    public function actionActDelete($id)
    {
        $model = $this->actParticipantRepository->get($id);
        $foreignEvent = $this->foreignEventRepository->get($model->foreign_event_id);
        $order = $this->orderEventRepository->get($foreignEvent->order_participant_id);
        $files = $this->filesRepository->getByDocument(ActParticipantWork::tableName(), $model->id);
        foreach ($files as $file) {
            $model->recordEvent(new FileDeleteEvent($file->id), DocumentOrderWork::class);
        }
        //act_participant_branch
        $model->recordEvent(new ActParticipantBranchDeleteEvent($model->id), DocumentOrderWork::class);
        //squad_participant
        $model->recordEvent(new SquadParticipantDeleteByIdEvent($model->id), DocumentOrderWork::class);
        //act_participant
        $model->recordEvent(new ActParticipantDeleteEvent($model->id), DocumentOrderWork::class);
        $model->releaseEvents();
        return $this->redirect(['update', 'id' => $order->id]);
    }
    public function actionDelete($id){
        $model = $this->documentOrderRepository->get($id);
        $this->documentOrderService->documentOrderDelete($model);
        $model->releaseEvents();
        return $this->redirect(['index']);
    }
    public function actionDeleteActFile($modelId, $fileId)
    {
        try {
            $file = $this->filesRepository->getById($fileId);

            /** @var FilesWork $file */
            $filepath = $file ? basename($file->filepath) : '';
            $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
            $file->recordEvent(new FileDeleteEvent($fileId), get_class($file));
            $file->releaseEvents();

            Yii::$app->session->setFlash('success', "Файл $filepath успешно удален");
            return $this->redirect(['act', 'id' => $modelId]);
        }
        catch (DomainException $e) {
            return $e->getMessage();
        }
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