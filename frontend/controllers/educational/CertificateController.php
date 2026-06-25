<?php

namespace frontend\controllers\educational;

use common\helpers\files\FilesHelper;
use common\repositories\educational\CertificateRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\CertificateTemplatesRepository;
use frontend\components\GroupParticipantWidget;
use frontend\components\wizards\CertificateWizard;
use frontend\forms\certificate\CertificateForm;
use frontend\models\search\SearchCertificate;
use frontend\models\work\educational\CertificateWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\services\educational\CertificateService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class CertificateController extends Controller
{
    private CertificateRepository $repository;
    private TrainingGroupParticipantRepository $participantRepository;
    private CertificateService $service;

    public function __construct(
        $id,
        $module,
        CertificateRepository $repository,
        TrainingGroupParticipantRepository $participantRepository,
        CertificateService $service,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->participantRepository = $participantRepository;
        $this->service = $service;
    }

    public function actionIndex()
    {
        $searchModel = new SearchCertificate();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Получаем все доступные шаблоны
        $templates = (Yii::createObject(CertificateTemplatesRepository::class))->getAll();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'templates' => $templates,
        ]);
    }

    public function actionView($id)
    {
        $model = new CertificateForm(null, null, $id);

        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionCreate($id = null)
    {
        $form = new CertificateForm(
            $this->service->buildGroupQuery($id),
            $this->service->buildParticipantQuery($id)
        );

        if ($form->load(Yii::$app->request->post())) {
            $certificateIds = $this->service->saveAllCertificates($form);
            $this->service->uploadCertificates($certificateIds);
            return $this->redirect(['download-archive']);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    public function actionDelete($id)
    {
        /** @var CertificateWork $certificate */
        $certificate = $this->repository->get($id);
        $this->repository->delete($certificate);
        Yii::$app->session->setFlash('success', 'Сертификат успешно удален');

        return $this->redirect(['index']);
    }

    public function actionChangeTemplate($id)
    {
        $form = new CertificateForm(
            $this->service->buildGroupQuery($id),
            $this->service->buildParticipantQuery($id),
            $id  // Передаем id существующего сертификата
        );

        if ($form->load(Yii::$app->request->post())) {
            // Обновляем только templateId у существующего сертификата
            $this->service->updateCertificateTemplate($form->entity, $form->templateId);

            Yii::$app->session->setFlash('success', 'Тип сертификата успешно изменен');
            return $this->redirect(['view', 'id' => $id]);
        }

        // Если не POST запрос, возвращаемся на страницу просмотра
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionMassChangeTemplate()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            $certificateIds = explode(',', $request->post('certificate_ids'));
            $templateId = $request->post('template_id');

            if (!empty($certificateIds) && $templateId) {
                $count = $this->service->massUpdateCertificateTemplate($certificateIds, $templateId);
                Yii::$app->session->setFlash('success', "Успешно обновлено {$count} сертификатов");
            } else {
                Yii::$app->session->setFlash('error', 'Не выбраны сертификаты или шаблон');
            }
        }

        return $this->redirect(['index']);
    }


    public function actionDownloadArchive()
    {
        $this->service->downloadCertificates();
        return $this->redirect(['index']);
    }

    public function actionDownloadAllCertificates($groupId) {
        $certificates = $this->service->getAllCertificatesByGroupId($groupId);
        $this->service->uploadCertificatesByCertificates($certificates);
        return $this->redirect(['download-archive']);
    }

    public function actionSendAll($groupId)
    {
        CertificateWizard::sendCertificates(
            $this->repository->getCertificatesByGroupId($groupId)
        );

        return $this->redirect(['/educational/training-group/view', 'id' => $groupId]);
    }

    public function actionSendPdf($id)
    {
        FilesHelper::createDirectory(Yii::$app->basePath . '/download/' . Yii::$app->user->identity->getId() . '_temp_certificates/');

        /** @var CertificateWork $model */
        $model = $this->repository->get($id);
        $result = CertificateWizard::sendCertificateToEmail($model);
        if ($result) {
            Yii::$app->session->setFlash('success', 'Сертификат успешно отправлен на адрес: ' . $model->trainingGroupParticipantWork->participantWork->email);
        }
        else {
            Yii::$app->session->setFlash('danger', 'Не удалось отправить сертификат на указанный адрес: '.$model->trainingGroupParticipantWork->participantWork->email);
        }
        FilesHelper::removeDirectory(Yii::$app->basePath . '/download/' . Yii::$app->user->identity->getId() . '_temp_certificates/');

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionGenerationPdf($id)
    {
        /** @var CertificateWork $model */
        $model = $this->repository->get($id);
        CertificateWizard::downloadCertificate(
            $model,
            $model->trainingGroupParticipantWork,
            CertificateWizard::DESTINATION_DOWNLOAD
        );
    }

    public function actionGetGroups()
    {
        return [];
    }

    public function actionGetParticipants()
    {
        $groupIds = json_decode(Yii::$app->request->get('groupIds'));

        return $this->asJson([
            'gridHtml' => $this->renderPartial(GroupParticipantWidget::GROUP_PARTICIPANT_VIEW, [
                'dataProvider' => new ActiveDataProvider([
                    'query' => $this->participantRepository->getParticipantsWithoutCertificates($groupIds),
                    'pagination' => false,
                ]),
            ]),
        ]);
    }
}