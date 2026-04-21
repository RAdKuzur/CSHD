<?php

namespace frontend\controllers;

use common\services\general\errors\ErrorService;
use frontend\forms\analytics\AnalyticErrorForm;
use yii\web\Controller;
use frontend\models\search\SearchErrors;
use yii\data\ArrayDataProvider;
use Yii;


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
        $searchModel = new SearchErrors();
        $searchModel->load(Yii::$app->request->queryParams);
        
        $allErrors = $this->errorService->getErrorsByUser($id);
        
        $modelErrors = new AnalyticErrorForm($allErrors);
        
        $modelErrors->setGroupErrors(
            $searchModel->search(Yii::$app->request->queryParams, $modelErrors->getGroupErrors())
        );
        $modelErrors->setProgramErrors(
            $searchModel->search(Yii::$app->request->queryParams, $modelErrors->getProgramErrors())
        );
        $modelErrors->setOrderErrors(
            $searchModel->search(Yii::$app->request->queryParams, $modelErrors->getOrderErrors())
        );
        $modelErrors->setEventErrors(
            $searchModel->search(Yii::$app->request->queryParams, $modelErrors->getEventErrors())
        );
        $modelErrors->setForeignEventErrors(
            $searchModel->search(Yii::$app->request->queryParams, $modelErrors->getForeignEventErrors())
        );
        $modelErrors->setForeignEventParticipantsErrors(
            $searchModel->search(Yii::$app->request->queryParams, $modelErrors->getForeignEventParticipantsErrors())
        );
        
        $branches = Yii::$app->branches->getList();
        
        return $this->render('errors', [
            'model' => $modelErrors,
            'searchModel' => $searchModel,
            'branches' => $branches,
            'id' => $id,
        ]);
    }
}