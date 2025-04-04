<?php

namespace backend\controllers;

use backend\builders\CertificateTemplatesBuilder;
use backend\forms\CertificateTemplatesForm;
use backend\services\CertificateTemplatesService;
use common\components\traits\AccessControl;
use common\repositories\educational\CertificateTemplatesRepository;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class CertificateTemplatesController extends Controller
{
    use AccessControl;

    private CertificateTemplatesBuilder $builder;
    private CertificateTemplatesRepository $repository;

    private CertificateTemplatesService $service;

    public function __construct(
        $id,
        $module,
        CertificateTemplatesBuilder $builder,
        CertificateTemplatesRepository $repository,
        CertificateTemplatesService $service,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->builder = $builder;
        $this->repository = $repository;
        $this->service = $service;
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->builder->query()
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = new CertificateTemplatesForm($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->fillEntity();
            $this->repository->save($model->entity);
            return $this->redirect(['view', 'id' => $model->entity->id]);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    public function actionDelete($id)
    {
        $result = $this->service->delete($id);
        if (!$result) {
            Yii::$app->session->setFlash('danger', 'Ошибка. Невозможно удалить шаблон, т.к. он используется в актуальных сертификатах');
        }
        return $this->redirect(['index']);
    }

    public function actionCreateTemplate()
    {
        $model = new CertificateTemplatesForm();

        if ($model->load(Yii::$app->request->post())) {
            $model->templateFile = UploadedFile::getInstance($model, 'templateFile');
            if (!is_null($model->templateFile)) {
                $model->fillEntity();
                $model->uploadTemplateFile();
                $this->repository->save($model->entity);
                return $this->redirect(['view', 'id' => $model->entity->id]);
            }
            Yii::$app->session->setFlash('danger', 'Невозможно добавить шаблон без подложки');
        }

        return $this->render('create-template', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        $model = new CertificateTemplatesForm($id);
        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionGetImage($filepath)
    {
        $path = Yii::getAlias('@app/../') . $filepath;
        if (file_exists($path)) {
            return Yii::$app->response->sendFile($path);
        } else {
            throw new NotFoundHttpException('Файл не найден');
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