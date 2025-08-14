<?php

namespace backend\controllers\report\form;

use backend\forms\report\DodForm;
use backend\forms\report\ECForm;
use backend\forms\report\SAForm;
use backend\invokables\ReportDodLoader;
use backend\invokables\ReportECLoader;
use backend\invokables\ReportSALoader;
use backend\services\report\form\DodReportService;
use backend\services\report\form\EffectiveContractReportService;
use backend\services\report\form\StateAssignmentReportService;
use backend\services\report\ReportFacade;
use common\helpers\DateFormatter;
use Yii;
use yii\web\Controller;

class FormReportController extends Controller
{
    private StateAssignmentReportService $stateAssignmentService;
    private DodReportService $dodReportService;
    private EffectiveContractReportService $effectiveContractReportService;

    public function __construct(
        $id,
        $module,
        StateAssignmentReportService $stateAssignmentService,
        DodReportService $dodReportService,
        EffectiveContractReportService $effectiveContractReportService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->stateAssignmentService = $stateAssignmentService;
        $this->dodReportService = $dodReportService;
        $this->effectiveContractReportService = $effectiveContractReportService;
    }

    public function actionFormList()
    {
        return $this->render('form-list');
    }

    public function actionDod()
    {
        $model = new DodForm();

        if ($model->load(Yii::$app->request->post())) {
            $loader = new ReportDodLoader(
                'report_DOD.xlsx',
                'DOD_report_' .
                        DateFormatter::format(date('Y-m-d'), DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator)
                        . '.xlsx',
                ReportFacade::generateDod($model, $this->dodReportService)
            );
            $loader();
        }

        return $this->render('dod', [
            'model' => $model
        ]);
    }

    public function actionStateAssignment()
    {
        $model = new SAForm();

        if ($model->load(Yii::$app->request->post())) {
            $loader = new ReportSALoader(
                'report_GZ.xlsx',
                'SA_report_' .
                        DateFormatter::format(date('Y-m-d'), DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator)
                        . '.xlsx',
                ReportFacade::generateSA($model, $this->stateAssignmentService)
            );
            $loader();
        }

        return $this->render('state-assignment', [
            'model' => $model
        ]);
    }
    public function actionEffectiveContract(){
        $model = new ECForm();
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $loader = new ReportECLoader(
                'report_EC.xlsx',
                'SA_report_' .
                DateFormatter::format(date('Y-m-d'), DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator)
                . '.xlsx',
                ReportFacade::generateEC($model, $this->effectiveContractReportService)
            );
            $loader();
        }
        return $this->render('effective-contract', [
            'model' => $model
        ]);
    }
}