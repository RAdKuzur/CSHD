<?php

namespace backend\controllers\report\query;

use backend\forms\report\ManHoursReportForm;
use backend\helpers\DebugReportHelper;
use backend\invokables\CsvLoader;
use backend\services\report\mock\ReportManHoursMockService;
use backend\services\report\ReportFacade;
use backend\services\report\ReportManHoursService;
use common\components\traits\AccessControl;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class ManHoursReportController extends Controller
{
    use AccessControl;

    private ReportManHoursService $service;

    public function __construct(
        $id,
        $module,
        ReportManHoursService $service,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    /**
     * @throws InvalidConfigException
     */
    public function actionManHours()
    {
        $form = Yii::createObject(ManHoursReportForm::class);
        if ($form->load(Yii::$app->request->post())) {
            $result = ReportFacade::generateManHoursReport($form, $this->service);

            return $this->render('man-hours-result', [
                'manHoursResult' => $result['manHours'] ?? [],
                'participantsResult' => $result['participants'] ?? []
            ]);
        }

        return $this->render('man-hours', [
            'model' => $form
        ]);
    }

    public function actionDownloadDebugCsv(string $type)
    {
        if (Yii::$app->request->isPost) {
            switch ($type) {
                case ManHoursReportForm::MAN_HOURS_REPORT:
                    $csvHeader = DebugReportHelper::getManHoursReportHeaders();
                    break;
                default:
                    $csvHeader = DebugReportHelper::getParticipantsReportHeaders();
            }
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