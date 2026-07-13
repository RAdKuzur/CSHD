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
use common\helpers\SortHelper;
use common\helpers\StringFormatter;
use common\models\scaffold\OrderEventGenerate;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\event\ParticipantAchievementRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\order\OrderEventGenerateRepository;
use common\services\general\errors\BatchCheckService;
use frontend\events\general\FileDeleteEvent;
use frontend\invokables\OrderLoader;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\event\ParticipantAchievementWork;
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
use frontend\models\work\auxiliary\OrderEventParticipantLoader; //добавлено

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
    private ParticipantAchievementRepository $participantAchievementRepository;

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
        ParticipantAchievementRepository $participantAchievementRepository,
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
        $this->participantAchievementRepository = $participantAchievementRepository;
        parent::__construct($id, $module, $fileService, $fileRepository, $config);
    }

    public function actionErrorsCheck()
    {
        set_time_limit(300);

        /** @var BatchCheckService $batchService */
        $batchService = Yii::createObject(BatchCheckService::class);

        $allOrderEvent = OrderEventWork::find()->all();

        if (empty($allOrderEvent)) {
            Yii::$app->session->setFlash('info', 'Нет записей для проверки');
            return $this->redirect(['index']);
        }

        $orderIds = ArrayHelper::getColumn($allOrderEvent, 'id');
        $errorList = ErrorAssociationHelper::getOrderEventErrorsList();

//        // Предзагружаем данные
//        $preloadedData = [];
//        foreach ($errorList as $errorCode) {
//            $errorEntity = Yii::$app->errors->get($errorCode);
//            if ($errorEntity->getDataFetchFunction() !== null) {
//                $preloadedData[$errorCode] = $errorEntity->fetchData($orderIds);
//            }
//        }

        // Предзагружаем данные
        $preloadedData = [];
        $errorEntity = Yii::$app->errors->get($errorList[0]);
        $firstData = $errorEntity->fetchData($orderIds); // Сохраняем данные в переменную

        // Удаляем первый элемент из массива
        unset($errorList[0]);

        // Для оставшихся кодов копируем те же данные
        foreach ($errorList as $errorCode) {
            $preloadedData[$errorCode] = $firstData;
        }

        // Предзагружаем ошибки таблицы
        $batchService->preloadTableErrors(OrderEventWork::tableName());
        $allCurrentErrors = $batchService->getPreloadedErrors();
        $allAmnestyErrors = $batchService->getPreloadedAmnestyErrors();

        $batchService->registerModels($allOrderEvent);
        $batchService->enableBatchMode();

        $total = count($allOrderEvent);
        $processed = 0;
        $totalSaved = 0;
        $totalDeleted = 0;
        $totalUpdated = 0;

        foreach ($allOrderEvent as $orderEvent) {
            $orderEvent->checkModelWithData(
                $errorList,
                OrderEventWork::tableName(),
                $orderEvent->id,
                $preloadedData,
                $allCurrentErrors,
                $allAmnestyErrors
            );

            $processed++;

            if ($processed % 5000 === 0) {
                $result = $batchService->flush();
                $totalSaved += $result['saved'];
                $totalDeleted += $result['deleted'];
                $totalUpdated += $result['updated'];
            }
        }

        $finalResult = $batchService->flush();
        $totalSaved += $finalResult['saved'];
        $totalDeleted += $finalResult['deleted'];
        $totalUpdated += $finalResult['updated'];

        $batchService->disableBatchMode();

        Yii::$app->session->setFlash('success',
            "Проверка завершена! Обработано: {$total} записей.\n" .
            "Исправлено ошибок: {$totalDeleted}\n" .
            "Новых ошибок: {$totalSaved}\n" .
            "Обновлено состояний: {$totalUpdated}"
        );

        return $this->redirect(['index']);
    }

    public function actionIndex() {
        $searchModel = new SearchOrderEvent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $linksFirst = ButtonsFormatter::primaryCreateLink('приказ');

        $links = array_merge(
            $linksFirst,
            ButtonsFormatter::anyOneLink('Проверить приказы на ошибки', Yii::$app->frontUrls::ORDER_EVENT_ERROR_CHECK,ButtonsFormatter::BTN_DANGER)
        );

        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml
        ]);
    }

    public function actionCreate()
    {
        $form = new OrderEventBuilderForm(
            new OrderEventForm(),
            $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO),
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
            $respPeopleId = DynamicWidget::getData(StringFormatter::getLastSegmentByBackslash(basename(OrderEventForm::class)), "responsible_id", $post);
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
            //$modelOrderEvent->generateOrderNumber();
            $error = $this->documentOrderService->generateNumber($modelOrderEvent);
            if (!$error){
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
                $modelOrderEvent->checkModel(ErrorAssociationHelper::getOrderEventErrorsList(), OrderEventWork::tableName(), $modelOrderEvent->id);
                $this->orderPeopleService->addOrderPeopleEvent($respPeopleId, $modelOrderEvent);
                $this->foreignEventService->saveActFilesFromModel($modelForeignEvent, $form->orderEventForm->actFiles, $number);
                $form->orderEventForm->releaseEvents();
                $modelForeignEvent->releaseEvents();
                $modelOrderEvent->releaseEvents();
                $this->actParticipantService->addActParticipant($acts, $modelForeignEvent->id);
                return $this->redirect(['view', 'id' => $modelOrderEvent->id]);
            }
            else {
                Yii::$app->session->setFlash
                ('error', "Ошибка создания приказа с такой датой");
                return $this->redirect(Yii::$app->request->referrer ?: ['create']);
            }
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
        $modelOrderEvent->checkFilesExist();
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($modelOrderEvent->id);
        $actTable = $this->actParticipantService->createActTable($foreignEvent->id);
        $modelOrderEvent->checkModel(ErrorAssociationHelper::getOrderEventErrorsList(), OrderEventWork::tableName(), $id);
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
                $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO),
                [new ActParticipantForm],
                $this->teamService->getNamesByForeignEventId($modelForeignEvent->id),
                array_unique(ArrayHelper::getColumn($this->actParticipantRepository->getByForeignEventIds([$modelForeignEvent->id]), 'nomination')),
                $this->foreignEventParticipantsRepository->getSortedList(ForeignEventParticipantsRepository::SORT_FIO),
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
                $modelOrderEvent->checkModel(ErrorAssociationHelper::getOrderEventErrorsList(), OrderEventWork::tableName(), $modelOrderEvent->id);
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
        try {
            $model = $this->actParticipantRepository->get($id);
            $foreignEvent = $this->foreignEventRepository->get($model->foreign_event_id);
            $order = $this->orderEventRepository->get($foreignEvent->order_participant_id);
            
            $achievements = $this->participantAchievementRepository->getByActIds(
                $id, 
                [ParticipantAchievementWork::TYPE_PRIZE, ParticipantAchievementWork::TYPE_WINNER]
            );
            
            if (empty($achievements)) {
                \frontend\models\work\team\SquadParticipantWork::deleteAll(['act_participant_id' => $id]);
                \frontend\models\work\team\ActParticipantBranchWork::deleteAll(['act_participant_id' => $id]);
                
                $files = $this->filesRepository->getByDocument(
                    \frontend\models\work\team\ActParticipantWork::tableName(), 
                    $model->id
                );
                foreach ($files as $file) {
                    if (method_exists($file, 'recordEvent')) {
                        $file->recordEvent(new \frontend\events\general\FileDeleteEvent($file->id), get_class($file));
                        $file->releaseEvents();
                    }
                    $file->delete();
                }
                
                $model->delete();
            }
            
            return $this->redirect(['update', 'id' => $order->id]);
            
        } catch (\Exception $e) {
            Yii::error('Ошибка удаления: ' . $e->getMessage(), __METHOD__);
            return $this->redirect(['update', 'id' => $order->id ?? 0]);
        }
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
        "Приказ №" . $model->getFullNumber() . ' ' . preg_replace('/[^\w\-]/u', '_', mb_substr($model->order_name, 0, 35))
        );
        $loader();
    }
    public function actionParticipantsList($q = null, $page = 1)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $perPage = 30;
        $offset = ($page - 1) * $perPage;

        $query = ForeignEventParticipantsWork::find()
            ->select(['id', 'CONCAT(surname, " ", firstname, " ", patronymic) AS fullFio'])
            ->where(['or',
                ['like', 'surname', $q],
                ['like', 'firstname', $q],
                ['like', 'patronymic', $q]
            ])
            ->orderBy('surname, firstname');

        $totalCount = $query->count();

        $participants = $query
            ->offset($offset)
            ->limit($perPage)
            ->asArray()
            ->all();

        $results = [];
        foreach ($participants as $participant) {
            $results[] = [
                'id' => $participant['id'],
                'text' => $participant['fullFio']
            ];
        }

        return [
            'items' => $results,
            'total_count' => $totalCount
        ];
    }
    //----------------------------------Добавлено---------------------------------------------
   public function actionLoadParticipantsFromExcel($id)
    {
        $orderEvent = $this->orderEventRepository->get($id);  // ID приказа
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($orderEvent->id);

        $loader = Yii::createObject(OrderEventParticipantLoader::class, [
            $orderEvent->id,       // ID приказа
            $foreignEvent->id,     // ID мероприятия
            Yii::$container->get(\common\repositories\dictionaries\PeopleRepository::class),
            Yii::$container->get(\common\repositories\dictionaries\ForeignEventParticipantsRepository::class),
            Yii::$container->get(\frontend\services\act_participant\ActParticipantService::class)
        ]);
        
        return $this->render('participants-load', [
            'model' => $loader,
            'orderId' => $orderEvent->id,        // ID приказа для формы
            'orderName' => $orderEvent->getFullNumber(), 
        ]);
    }

    public function actionProcessParticipantsExcel($id)
    {
        $orderEvent = $this->orderEventRepository->get($id);
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($orderEvent->id);

        $model = Yii::createObject(OrderEventParticipantLoader::class, [
            $orderEvent->id,
            $foreignEvent->id,
            Yii::$container->get(\common\repositories\dictionaries\PeopleRepository::class),
            Yii::$container->get(\common\repositories\dictionaries\ForeignEventParticipantsRepository::class),
            Yii::$container->get(\frontend\services\act_participant\ActParticipantService::class)
        ]);

        if (Yii::$app->request->isPost) {
            
            $model->file = \yii\web\UploadedFile::getInstance($model, 'file');

            if ($model->validate() && $model->file) {
                try {
                    $result = $model->processFile();
                    
                    $hasDuplicates = false;
                    foreach ($result['errors'] as $error) {
                        if (strpos($error, 'уже есть') !== false) {
                            $hasDuplicates = true;
                            break;
                        }
                    }
                    
                    if ($result['success']) {
                        if ($hasDuplicates) {
                            Yii::$app->session->setFlash('warning', 
                                'Было записано ' . $result['processed'] . ' строк, из ' . $result['total'] .
                                ' (некоторые участники уже были добавлены ранее)'
                            );
                        } else {
                            Yii::$app->session->setFlash('success', 
                                'Было записано ' . $result['processed'] . ' строк, из ' . $result['total']
                            );
                        }
                    } else if ($result['processed'] > 0) {
                        Yii::$app->session->setFlash('warning', 
                            'Было записано ' . $result['processed'] . ' строк, из ' . $result['total']
                        );
                    } else {
                        if ($hasDuplicates && count($result['errors']) == $result['total']) {
                            Yii::$app->session->setFlash('warning', 
                                'Все участники уже были добавлены в этот приказ ранее. ' .
                                'Повторно не добавлено: ' . $result['total'] . ' строк.'
                            );
                        } else {
                            Yii::$app->session->setFlash('error', 
                                'Было записано ' . $result['processed'] . ' строк, из ' . $result['total']
                            );
                        }
                    }
                    
                    if (!empty($result['errors'])) {
                        Yii::$app->session->setFlash('importErrors', $result['errors']);
                    }
                    
                    return $this->redirect(['view', 'id' => $id]);
                    
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Ошибка: ' . $e->getMessage());
                    return $this->redirect(['view', 'id' => $id]);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка валидации файла');
                return $this->redirect(['load-participants-from-excel', 'id' => $id]);
            }
        }
        
        return $this->redirect(['load-participants-from-excel', 'id' => $id]);
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