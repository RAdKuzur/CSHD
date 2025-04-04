<?php

namespace backend\controllers\report\query;

use backend\forms\report\ForeignEventReportForm;
use backend\helpers\DebugReportHelper;
use backend\invokables\CsvLoader;
use backend\services\report\ReportFacade;
use backend\services\report\ReportForeignEventService;
use common\components\traits\AccessControl;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class ForeignEventReportController extends Controller
{
    use AccessControl;

    private ReportForeignEventService $service;

    public function __construct(
        $id,
        $module,
        ReportForeignEventService $service,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    /**
     * @throws InvalidConfigException
     */
    public function actionForeignEvent()
    {
        $form = Yii::createObject(ForeignEventReportForm::class);
        if ($form->load(Yii::$app->request->post())) {
            $result = ReportFacade::generateParticipantsReport($form, $this->service);

            return $this->render('foreign-event-result', [
                'eventResult' => $result ?? []
            ]);
        }

        return $this->render('foreign-event', [
            'model' => $form
        ]);
    }

    public function actionDownloadDebugCsv()
    {
        if (Yii::$app->request->isPost) {
            $csvHeader = DebugReportHelper::getEventReportHeaders();
            $loader = new CsvLoader($csvHeader, Yii::$app->request->post()['debugData']);
            $loader();
        }
        else {
            throw new BadRequestHttpException('Для данного эндпоинта допустимы только POST-запросы');
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