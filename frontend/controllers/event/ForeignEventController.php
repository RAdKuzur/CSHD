<?php

namespace frontend\controllers\event;

use common\components\traits\AccessControl;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\helpers\ButtonsFormatter;
use common\helpers\ErrorAssociationHelper;
use common\helpers\html\HtmlBuilder;
use common\Model;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\event\ParticipantAchievementRepository;
use common\repositories\general\FilesRepository;
use common\repositories\order\OrderEventRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\forms\event\EventParticipantForm;
use frontend\forms\event\ForeignEventForm;
use frontend\forms\event\ParticipantAchievementForm;
use frontend\models\search\SearchForeignEvent;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\general\PeopleWork;
use frontend\services\event\ForeignEventService;
use Yii;
use yii\web\Controller;

class ForeignEventController extends DocumentController
{
    use AccessControl;

    private ForeignEventService $service;
    private OrderEventRepository $orderEventRepository;
    private PeopleRepository $peopleRepository;
    private ParticipantAchievementRepository $achievementRepository;
    private LockWizard $lockWizard;

    public function __construct(
        $id,
        $module,
        ForeignEventService $service,
        OrderEventRepository $orderEventRepository,
        PeopleRepository $peopleRepository,
        ParticipantAchievementRepository $achievementRepository,
        LockWizard $lockWizard,
        $config = [])
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->service = $service;
        $this->lockWizard = $lockWizard;
        $this->orderEventRepository = $orderEventRepository;
        $this->peopleRepository = $peopleRepository;
        $this->achievementRepository = $achievementRepository;
    }

    public function actionIndex()
    {
        $searchModel = new SearchForeignEvent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, ForeignEventWork::tableName(), Yii::$app->user->id)) {
            $form = new ForeignEventForm($id);

            if ($form->load(Yii::$app->request->post())) {
                $this->lockWizard->unlockObject($id, ForeignEventWork::tableName());
                $modelAchievements = Model::createMultiple(ParticipantAchievementWork::classname());
                Model::loadMultiple($modelAchievements, Yii::$app->request->post());
                if (Model::validateMultiple($modelAchievements, ['act_participant_id', 'achievement', 'cert_number', 'date', 'type'])) {
                    $form->newAchievements = $modelAchievements;
                }

                $this->service->attachAchievement($form);
                $this->service->getFilesInstances($form);
                $this->service->saveAchievementFileFromModel($form);
                $form->save();
                $form->releaseEvents();
                $form->event->checkModel(ErrorAssociationHelper::getForeignEventErrorsList(), ForeignEventWork::tableName(), $id);
                return $this->redirect(['view', 'id' => $id]);
            }

            return $this->render('update', [
                'model' => $form,
                'peoples' => $this->peopleRepository->getAll(),
                'orders6' => $this->orderEventRepository->getEventOrdersByLastTime(date('Y-m-d', strtotime($form->startDate . '-6 month'))),
                'orders9' => $this->orderEventRepository->getEventOrdersByLastTime(date('Y-m-d', strtotime($form->startDate . '-9 month'))),
                'modelAchievements' => [new ParticipantAchievementWork],
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, ForeignEventWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionView($id)
    {
        $form = new ForeignEventForm($id);
        $form->event->checkFilesExist();

        $links = array_merge(
            ButtonsFormatter::updateDeleteLinks($id),
            ButtonsFormatter::anyOneLink('Простить ошибки', 'amnesty', ButtonsFormatter::BTN_WARNING)
        );
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('view',[
            'model' => $form,
            'buttonsAct' => $buttonHtml
        ]);
    }

    public function actionUpdateParticipant($id, $modelId)
    {
        $form = new EventParticipantForm($id);

        if ($form->load(Yii::$app->request->post())) {
            if (!$form->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($form->getErrors()));
            }

            $this->service->getParticipantFilesInstances($form);
            $this->service->saveParticipantFileFromModel($form);
            $form->save();
            $form->releaseEvents();

            return $this->redirect(['update', 'id' => $modelId]);
        }

        return $this->render('update-participant', [
            'model' => $form
        ]);
    }

    public function actionUpdateAchievement($id, $modelId)
    {
        $form = new ParticipantAchievementForm($id);

        if ($form->load(Yii::$app->request->post())) {
            if (!$form->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($form->getErrors()));
            }

            $form->save();
            return $this->redirect(['update', 'id' => $modelId]);
        }

        return $this->render('update-achievement', [
            'model' => $form
        ]);
    }

    public function actionDeleteAchievement($id, $modelId)
    {
        /** @var ParticipantAchievementWork $model */
        $model = $this->achievementRepository->get($id);
        $result = $this->achievementRepository->delete($model);
        if ($result) {
            Yii::$app->session->setFlash('success', 'Достижение успешно удалено');
        }
        else {
            Yii::$app->session->setFlash('danger', 'Возникла ошибка при удалении достижения');
        }

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