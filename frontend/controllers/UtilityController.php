<?php

namespace frontend\controllers;

use common\components\wizards\LockWizard;
use Yii;
use yii\web\Controller;

class UtilityController extends Controller
{
    private LockWizard $lockWizard;

    public function __construct(
        $id,
        $module,
        LockWizard $lockWizard,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->lockWizard = $lockWizard;
    }

    /* -- Экшны для блокировки/разблокировки ресурсов -- */

    public function actionRefreshLock()
    {
        $data = Yii::$app->request->post();
        $this->lockWizard->refreshLock($data['objectId'], $data['objectType']);
        return $this->asJson(['success' => true]);
    }

    /**
     * Снятие блокировки с объекта
     *
     * @param int $type 0 - уход со страницы, 1 - автопереход по отсутствию активности
     * @return \yii\web\Response
     */
    public function actionUnlock($type = 1)
    {
        $data = Yii::$app->request->post();
        $this->lockWizard->unlockObject($data['objectId'], $data['objectType']);
        if ($type == 1) {
            Yii::$app->session->setFlash('danger', 'Из-за отсутствия активности Вы были перенаправлены на страницу просмотра');
        }
        return $this->asJson(['success' => true]);
    }

    /* ------------------------------------------------- */
}