<?php

namespace backend\controllers;

use common\invokables\ErrorsSender;
use common\models\Error;
use common\models\work\ErrorsWork;
use common\models\work\UserWork;
use common\repositories\general\ErrorsRepository;
use common\repositories\general\UserRepository;
use common\repositories\rubac\PermissionTokenRepository;
use common\services\general\errors\ErrorService;
use frontend\models\work\rubac\PermissionTokenWork;
use Yii;
use yii\web\Controller;

class DaemonController extends Controller
{
    private ErrorsRepository $errorsRepository;
    private PermissionTokenRepository $tokenRepository;
    private UserRepository $userRepository;

    private ErrorService $errorService;

    public function __construct(
        $id,
        $module,
        ErrorsRepository $errorsRepository,
        PermissionTokenRepository $tokenRepository,
        UserRepository $userRepository,
        ErrorService $errorService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->errorsRepository = $errorsRepository;
        $this->tokenRepository = $tokenRepository;
        $this->userRepository = $userRepository;
        $this->errorService = $errorService;
    }

    // Эндпоинт обновления статусов ошибок для демона
    public function actionChangeErrorsState()
    {
        /** @var ErrorsWork[] $errors */
        $errors = $this->errorsRepository->getChangeableErrors();

        foreach ($errors as $error) {
            /** @var Error $errorEntity */
            $errorEntity = Yii::$app->errors->get($error->error);
            $errorEntity->changeState($error->id);
        }
    }

    // Эндпоинт рассылки критических ошибок на e-mail
    public function actionSendErrorsByEmail()
    {
        /** @var UserWork[] $users */
        $users = $this->userRepository->getAll();

        foreach ($users as $user) {
            /** @var ErrorsWork[] $errors */
            $errors = $this->errorService->getErrorsByUser($user->id);
            if (count($errors) > 0) {
                $sender = new ErrorsSender(
                    $user->email,
                    $errors
                );
                $sender();
            }
        }
    }

    // Эндпоинт очистки протухших токенов (временных разрешений)
    public function actionRemoveTokenPermissions()
    {
        /** @var PermissionTokenWork[] $tokens */
        $tokens = $this->tokenRepository->getAll();

        foreach ($tokens as $token) {
            if (strtotime('now') > strtotime($token->end_time)) {
                $this->tokenRepository->delete($token);
            }
        }
    }
}