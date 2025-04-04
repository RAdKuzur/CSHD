<?php

namespace frontend\controllers;

use common\services\general\errors\ErrorService;
use frontend\forms\analytics\AnalyticErrorForm;
use yii\web\Controller;

class AnalyticsController extends Controller
{
    private ErrorService $errorService;

    public function __construct(
        $id,
        $module,
        ErrorService $errorService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->errorService = $errorService;
    }

    public function actionErrors($id)
    {
        $modelErrors = new AnalyticErrorForm($this->errorService->getErrorsByUser($id));

        return $this->render('errors', [
            'model' => $modelErrors
        ]);
    }
}