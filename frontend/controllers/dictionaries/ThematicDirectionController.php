<?php

namespace frontend\controllers\dictionaries;

use common\components\traits\AccessControl;
use yii\web\Controller;

class ThematicDirectionController extends Controller
{
    use AccessControl;


    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex() {
        return $this->render('index');
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