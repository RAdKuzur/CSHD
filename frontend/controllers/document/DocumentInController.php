<?php

namespace frontend\controllers\document;

use common\components\traits\AccessControl;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\helpers\ButtonsFormatter;
use common\helpers\ErrorAssociationHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\SortHelper;
use common\helpers\StringFormatter;
use common\repositories\dictionaries\CompanyRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\dictionaries\PositionRepository;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use common\services\general\PeopleStampService;
use DomainException;
use frontend\events\document_in\InOutDocumentCreateEvent;
use frontend\events\document_in\InOutDocumentDeleteEvent;
use frontend\events\document_in\InOutDocumentUpdateEvent;
use frontend\models\search\SearchDocumentIn;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\services\document\DocumentInService;
use Yii;

class DocumentInController extends DocumentController
{
    use AccessControl;

    private DocumentInRepository $repository;
    private InOutDocumentsRepository $inOutRepository;
    private PeopleRepository $peopleRepository;
    private PositionRepository $positionRepository;
    private CompanyRepository $companyRepository;
    private DocumentInService $service;
    private PeopleStampService $peopleStampService;
    private LockWizard $lockWizard;
    private ButtonsFormatter $buttonsRepository;

    public function __construct(
        $id,
        $module,
        DocumentInRepository $repository,
        InOutDocumentsRepository $inOutRepository,
        PeopleRepository $peopleRepository,
        PositionRepository $positionRepository,
        CompanyRepository $companyRepository,
        DocumentInService $service,
        PeopleStampService $peopleStampService,
        LockWizard $lockWizard,
        ButtonsFormatter $buttonsRepository,
        $config = [])
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->repository = $repository;
        $this->inOutRepository = $inOutRepository;
        $this->peopleRepository = $peopleRepository;
        $this->positionRepository = $positionRepository;
        $this->companyRepository = $companyRepository;
        $this->service = $service;
        $this->peopleStampService = $peopleStampService;
        $this->lockWizard = $lockWizard;
        $this->buttonsRepository = $buttonsRepository;
    }

    public function actionIndex()
    {
        $model = new DocumentInWork();
        $searchModel = new SearchDocumentIn();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post())){
            $this->repository->createReserve($model);
            $this->repository->save($model);
        }

        $links = ButtonsFormatter::twoPrimaryLinks(Yii::$app->frontUrls::DOC_IN_CREATE, Yii::$app->frontUrls::DOC_IN_RESERVE);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    public function actionView($id)
    {
        $links = ButtonsFormatter::updateDeleteLinks($id);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        /** @var DocumentInWork $model */
        $model = $this->repository->get($id);
        $model->checkFilesExist();

        return $this->render('view', [
            'model' => $model,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    public function actionCreate()
    {
        $model = new DocumentInWork();
        $correspondentList = $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO);
        $availablePositions = $this->positionRepository->getList();
        $availableCompanies = $this->companyRepository->getList();
        $mainCompanyWorkers = $this->peopleRepository->getPeopleFromMainCompany();
        if ($model->load(Yii::$app->request->post())) {
            $model->generateDocumentNumber();
            $this->service->getPeopleStamps($model);

            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->repository->save($model);
            if ($model->needAnswer) {
                $model->recordEvent(new InOutDocumentCreateEvent($model->id, null, $model->dateAnswer, $model->nameAnswer), DocumentInWork::class);
            }
            $this->service->getFilesInstances($model);
            $this->service->saveFilesFromModel($model);
            $model->releaseEvents();
            $model->checkModel(ErrorAssociationHelper::getDocumentInErrorsList(), DocumentInWork::tableName(), $model->id);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'correspondentList' => $correspondentList,
            'availablePositions' => $availablePositions,
            'availableCompanies' => $availableCompanies,
            'mainCompanyWorkers' => $mainCompanyWorkers,
        ]);
    }
    public function actionReserve()
    {
        $model = new DocumentInWork();
        $this->repository->createReserve($model);
        $model->generateDocumentNumber();
        $this->repository->save($model);
        return $this->redirect(['index']);
    }

    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, DocumentInWork::tableName(), Yii::$app->user->id)) {
            $model = $this->repository->get($id);
            /** @var DocumentInWork $model */
            $model->setValuesForUpdate();

            $correspondentList = $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO);
            $availablePositions = $this->positionRepository->getList($model->correspondentWork->people_id);
            $availableCompanies = $this->companyRepository->getList($model->correspondentWork->people_id);
            $mainCompanyWorkers = $this->peopleRepository->getPeopleFromMainCompany();
            $tables = $this->service->getUploadedFilesTables($model);
            if ($model->load(Yii::$app->request->post())) {
                $this->lockWizard->unlockObject($id, DocumentInWork::tableName());
                $this->service->getPeopleStamps($model);

                if (!$model->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
                }
                $this->repository->save($model);
                if ($model->needAnswer) {
                    if ($this->inOutRepository->getByDocumentInId($model->id)){
                        $model->recordEvent(
                            new InOutDocumentUpdateEvent(
                                $model->id,
                                null,
                                $model->dateAnswer,
                                $model->nameAnswer
                            ),
                            DocumentInWork::class
                        );
                    }
                    else {
                        $model->recordEvent(
                            new InOutDocumentCreateEvent(
                                $model->id,
                                null,
                                $model->dateAnswer,
                                $model->nameAnswer
                            ),
                            DocumentInWork::class
                        );
                    }

                }
                else {
                    $model->recordEvent(new InOutDocumentDeleteEvent($model->id), DocumentInWork::class);
                }
                $this->service->getFilesInstances($model);
                $this->service->saveFilesFromModel($model);
                $model->releaseEvents();
                $model->checkModel(ErrorAssociationHelper::getDocumentInErrorsList(), DocumentInWork::tableName(), $model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('update', [
                'model' => $model,
                'correspondentList' => $correspondentList,
                'availablePositions' => $availablePositions,
                'availableCompanies' => $availableCompanies,
                'mainCompanyWorkers' => $mainCompanyWorkers,
                'scanFile' => $tables['scan'],
                'docFiles' => $tables['doc'],
                'appFiles' => $tables['app'],
            ]);
        }
        else {
            Yii::$app->session->setFlash
                ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, DocumentInWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionDelete($id)
    {
        $model = $this->repository->get($id);
        $number = $model->fullNumber;
        if ($model) {
            $this->repository->delete($model);
            Yii::$app->session->setFlash('success', "Документ $number успешно удален");
            return $this->redirect(['index']);
        }
        else {
            throw new DomainException('Модель не найдена');
        }
    }

    public function actionDependencyDropdown()
    {
        $id = Yii::$app->request->post('id');
        $response = '';

        if ($id === '') {
            // Получаем позиции и компании
            $response .= HtmlBuilder::buildOptionList($this->positionRepository->getList());
            $response .= "|split|";
            $response .= HtmlBuilder::buildOptionList($this->companyRepository->getList());
        } else {
            // Получаем позиции для указанного ID
            $positions = $this->positionRepository->getList($id);
            $response .= count($positions) > 0 ? HtmlBuilder::buildOptionList($positions) : HtmlBuilder::createEmptyOption();
            $response .= "|split|";
            // Получаем компанию для указанного ID
            $companies = $this->companyRepository->getList($id);
            $response .= count($companies) > 0 ? HtmlBuilder::buildOptionList($companies) : HtmlBuilder::createEmptyOption();
        }

        echo $response;
        exit;
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