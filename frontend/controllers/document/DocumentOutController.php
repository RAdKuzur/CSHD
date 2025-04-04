<?php

namespace frontend\controllers\document;
use common\components\traits\AccessControl;
use common\components\wizards\LockWizard;
use common\helpers\ButtonsFormatter;
use common\helpers\common\HeaderWizard;
use common\helpers\DateFormatter;
use common\helpers\ErrorAssociationHelper;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\SortHelper;
use common\repositories\dictionaries\CompanyRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\dictionaries\PositionRepository;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use common\services\general\PeopleStampService;
use DomainException;
use frontend\events\document_in\InOutDocumentDeleteEvent;
use frontend\events\document_in\InOutDocumentUpdateEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\models\search\SearchDocumentOut;
use frontend\models\work\document_in_out\DocumentOutWork;
use frontend\models\work\general\FilesWork;
use frontend\services\document\DocumentOutService;
use Yii;
use yii\web\Controller;

class DocumentOutController extends Controller
{
    use AccessControl;

    private DocumentOutRepository $repository;
    private PeopleRepository $peopleRepository;
    private PositionRepository $positionRepository;
    private CompanyRepository $companyRepository;
    private FileService $fileService;
    private PeopleStampService $peopleStampService;
    private LockWizard $lockWizard;
    private FilesRepository $filesRepository;
    private DocumentOutService $service;

    public function __construct(
        $id,
        $module,
        DocumentOutRepository $repository,
        PeopleRepository $peopleRepository,
        PositionRepository $positionRepository,
        CompanyRepository $companyRepository,
        FileService $fileService,
        PeopleStampService $peopleStampService,
        LockWizard $lockWizard,
        FilesRepository $filesRepository,
        DocumentOutService $service,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->peopleRepository = $peopleRepository;
        $this->positionRepository = $positionRepository;
        $this->companyRepository = $companyRepository;
        $this->fileService = $fileService;
        $this->filesRepository = $filesRepository;
        $this->service = $service;
        $this->peopleStampService = $peopleStampService;
        $this->lockWizard = $lockWizard;
    }
    public function actionIndex()
    {
        $model = new DocumentOutWork();
        $searchModel = new SearchDocumentOut();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $people = $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO);
        if ($model->load(Yii::$app->request->post())){
            $model->generateDocumentNumber();
            $this->repository->createReserve($model);
            $this->repository->save($model);
        }

        $links = ButtonsFormatter::primaryLinkAndModal(Yii::$app->frontUrls::DOC_OUT_CREATE, '#modal-reserve');
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'model' => $model,
            'peopleList' => $people,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml,
        ]);
    }
    public function actionView($id)
    {
        $links = ButtonsFormatter::updateDeleteLinks($id);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('view', [
            'model' => $this->repository->get($id),
            'buttonsAct' => $buttonHtml,
        ]);
    }
    public function actionCreate(){

        $model = new DocumentOutWork();
        $correspondentList = $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO);
        $availablePositions = $this->positionRepository->getList();
        $availableCompanies = $this->companyRepository->getList();
        $mainCompanyWorkers = $this->peopleRepository->getPeopleFromMainCompany();
        $filesAnswer = $this->repository->getDocumentInWithoutAnswer();
        if ($model->load(Yii::$app->request->post())) {
            $local_id = $model->getAnswer();
            $model->generateDocumentNumber();
            $this->service->getPeopleStamps($model);

            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->repository->save($model);
            if ($model->isAnswer) {
                $model->recordEvent(
                    new InOutDocumentUpdateEvent(
                        $local_id,
                        $model->id,
                        DateFormatter::format($model->dateAnswer, DateFormatter::dmY_dot, DateFormatter::Ymd_dash),
                        $model->nameAnswer
                    ),
                    DocumentOutWork::class
                );
            }
            $this->service->getFilesInstances($model);
            $this->service->saveFilesFromModel($model);
            $model->releaseEvents();
            $model->checkModel(ErrorAssociationHelper::getDocumentOutErrorsList(), DocumentOutWork::tableName(), $model->id);
            $this->service->checkDocumentInErrors($model->id);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'correspondentList' => $correspondentList,
            'availablePositions' => $availablePositions,
            'availableCompanies' => $availableCompanies,
            'mainCompanyWorkers' => $mainCompanyWorkers,
            'filesAnswer' => $filesAnswer
        ]);
    }
    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, DocumentOutWork::tableName(), Yii::$app->user->id)) {
            $model = $this->repository->get($id);
            /** @var DocumentOutWork $model */
            $model->setValuesForUpdate();

            $correspondentList = $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO);
            $availablePositions = $this->positionRepository->getList($model->correspondentWork->people_id);
            $availableCompanies = $this->companyRepository->getList($model->correspondentWork->people_id);
            $mainCompanyWorkers = $this->peopleRepository->getPeopleFromMainCompany();
            $tables = $this->service->getUploadedFilesTables($model);
            $filesAnswer = $this->repository->getDocumentInWithoutAnswer();
            if ($model->load(Yii::$app->request->post())) {
                $this->lockWizard->unlockObject($id, DocumentOutWork::tableName());
                $local_id = $model->getAnswer();
                $this->service->getPeopleStamps($model);

                if (!$model->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
                }
                $this->repository->save($model);
                if ($model->isAnswer) {
                    $model->recordEvent(
                        new InOutDocumentUpdateEvent(
                            $local_id,
                            $model->id,
                            DateFormatter::format($model->dateAnswer, DateFormatter::dmY_dot, DateFormatter::Ymd_dash),
                            $model->nameAnswer
                        ),
                        DocumentOutWork::class
                    );
                } else {
                    $model->recordEvent(new InOutDocumentDeleteEvent($model->id), DocumentOutWork::class);
                }
                $this->service->getFilesInstances($model);
                $this->service->saveFilesFromModel($model);
                $model->releaseEvents();
                $model->checkModel(ErrorAssociationHelper::getDocumentOutErrorsList(), DocumentOutWork::tableName(), $model->id);
                $this->service->checkDocumentInErrors($model->id);
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
                'filesAnswer' => $filesAnswer
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, DocumentOutWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }
    public function actionDelete($id)
    {
        /** @var DocumentOutWork $model */
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
    public function actionGetFile($filepath)
    {
        $data = $this->fileService->downloadFile($filepath);
        if ($data['type'] == FilesHelper::FILE_SERVER) {
            Yii::$app->response->sendFile($data['obj']->file);
        }
        else {
            $fp = fopen('php://output', 'r');
            HeaderWizard::setFileHeaders(FilesHelper::getFilenameFromPath($data['obj']->filepath), $data['obj']->file->size);
            $data['obj']->file->download($fp);
            fseek($fp, 0);
        }
    }
    public function actionDeleteFile($modelId, $fileId)
    {
        try {
            $file = $this->filesRepository->getById($fileId);

            /** @var FilesWork $file */
            $filepath = $file ? basename($file->filepath) : '';
            $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
            $file->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            $file->releaseEvents();

            Yii::$app->session->setFlash('success', "Файл $filepath успешно удален");
            return $this->redirect(['update', 'id' => $modelId]);
        }
        catch (DomainException $e) {
            return 'Oops! Something wrong';
        }
    }
    public function actionDependencyDropdown()
    {
        $id = Yii::$app->request->post('id');
        $response = '';

        if ($id === '') {
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

        return parent::beforeAction($action);;
    }
}