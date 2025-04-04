<?php


namespace frontend\controllers\educational;


use common\components\traits\AccessControl;
use common\helpers\DateFormatter;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\services\educational\PitchService;
use Yii;
use yii\web\Controller;

class PitchController extends Controller
{
    use AccessControl;

    private GroupProjectThemesRepository $projectThemesRepository;
    private PitchService $service;

    public function __construct(
        $id,
        $module,
        PitchService $service,
        GroupProjectThemesRepository $projectThemesRepository,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->projectThemesRepository = $projectThemesRepository;
    }

    public function actionIndex()
    {
        $data = $this->service->getSplittedGroups();

        return $this->render('index', [
            'progress' => $data['progress'],
            'finished' => $data['finished']
        ]);
    }

    public function actionConfirmTheme($id)
    {
        /** @var GroupProjectThemesWork $theme */
        $theme = $this->projectThemesRepository->get($id);
        $theme->setConfirm(GroupProjectThemesWork::CONFIRM);
        $this->projectThemesRepository->save($theme);
        Yii::$app->session->setFlash('success', "Тема проекта '{$theme->projectThemeWork->name}' подтверждена");

        return $this->redirect(['index']);
    }

    public function actionDeclineTheme($id)
    {
        /** @var GroupProjectThemesWork $theme */
        $theme = $this->projectThemesRepository->get($id);
        $theme->setConfirm(GroupProjectThemesWork::NO_CONFIRM);
        $this->projectThemesRepository->save($theme);
        Yii::$app->session->setFlash('danger', "Тема проекта '{$theme->projectThemeWork->name}' отклонена");

        return $this->redirect(['index']);
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