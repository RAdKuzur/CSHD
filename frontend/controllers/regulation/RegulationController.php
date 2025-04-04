<?php

namespace frontend\controllers\regulation;

use common\components\traits\AccessControl;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\helpers\ButtonsFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\repositories\general\FilesRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\models\search\SearchRegulation;
use frontend\models\work\regulation\RegulationWork;
use frontend\services\regulation\RegulationService;
use Yii;

class RegulationController extends DocumentController
{
    use AccessControl;

    private RegulationRepository $repository;
    private RegulationService $service;
    private LockWizard $lockWizard;

    public function __construct(
        $id,
        $module,
        RegulationRepository $repository,
        RegulationService    $service,
        LockWizard           $lockWizard,
        $config = [])
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->repository = $repository;
        $this->service = $service;
        $this->lockWizard = $lockWizard;
    }

    public function actionIndex()
    {
        $searchModel = new SearchRegulation();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = ButtonsFormatter::primaryCreateLink('положение');
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    public function actionView($id)
    {
        $links = ButtonsFormatter::updateDeleteLinks($id);
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        /** @var RegulationWork $model */
        $model = $this->repository->get($id);
        $model->checkFilesExist();

        return $this->render('view', [
            'model' => $model,
            'buttonsAct' => $buttonHtml,
        ]);
    }

    public function actionCreate()
    {
        $model = new RegulationWork();

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

    //
    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, RegulationWork::tableName(), Yii::$app->user->id)) {
            $model = $this->repository->get($id);
            /** @var RegulationWork $model */
            $fileTables = $this->service->getUploadedFilesTables($model);

            if ($model->load(Yii::$app->request->post())) {
                $this->lockWizard->unlockObject($id, RegulationWork::tableName());
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
                'scanFile' => $fileTables['scan'],
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, RegulationWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionDelete($id)
    {
        $model = $this->repository->get($id);
        $name = $model->name;
        if ($model) {
            $this->repository->delete($model);
            Yii::$app->session->setFlash('success', "Положение \"$name\" успешно удалено");
            return $this->redirect(['index']);
        }
        else {
            throw new DomainException('Модель не найдена');
        }
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