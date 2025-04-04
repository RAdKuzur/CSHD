<?php

namespace frontend\controllers\user;

use common\models\work\ErrorsWork;
use common\models\work\UserWork;
use common\repositories\general\UserRepository;
use common\repositories\rubac\PermissionFunctionRepository;
use common\repositories\rubac\PermissionTokenRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use common\services\general\errors\ErrorService;
use DateTime;
use DomainException;
use frontend\forms\analytics\AnalyticErrorForm;
use frontend\forms\ChangePasswordForm;
use frontend\forms\ErrorsForm;
use frontend\forms\TokenForm;
use frontend\models\work\rubac\PermissionTokenWork;
use Yii;
use yii\web\Controller;

class LkController extends Controller
{
    private UserRepository $userRepository;
    private PermissionFunctionRepository $permissionFunctionRepository;
    private PermissionTokenRepository $permissionTokenRepository;
    private UserPermissionFunctionRepository $userPermissionFunctionRepository;
    private ErrorService $errorService;
    public function __construct(
        $id,
        $module,
        UserRepository $userRepository,
        PermissionFunctionRepository $permissionFunctionRepository,
        PermissionTokenRepository $permissionTokenRepository,
        UserPermissionFunctionRepository $userPermissionFunctionRepository,
        ErrorService $errorService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->userRepository = $userRepository;
        $this->permissionFunctionRepository = $permissionFunctionRepository;
        $this->permissionTokenRepository = $permissionTokenRepository;
        $this->userPermissionFunctionRepository = $userPermissionFunctionRepository;
        $this->errorService = $errorService;
    }

    public function actionInfo(int $id)
    {
        $model = $this->userRepository->get($id);
        $permissions = $this->userPermissionFunctionRepository->getPermissionsByUser($id);
        return $this->render('info', [
            'model' => $model,
            'permissions' => $permissions
        ]);
    }

    public function actionChangePassword(int $id)
    {
        $model = new ChangePasswordForm();
        /** @var UserWork $user */
        $user = $this->userRepository->get($id);

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            if (!$user->validatePassword($model->oldPass)) {
                Yii::$app->session->setFlash('danger', 'Указан некорректный текущий пароль');
            }
            else {
                $this->userRepository->changePassword($model->newPass, $id);
                Yii::$app->session->setFlash('success', 'Пароль успешно изменен');

                return $this->render('info', [
                    'model' => $user,
                ]);
            }
        }

        return $this->render('change-password', [
            'model' => $model,
            'user' => $user,
        ]);
    }
    public function actionToken($id)
    {
        $user = $this->userRepository->get($id);
        $users = $this->userRepository->getAll();
        $permissions = $this->permissionFunctionRepository->getAllPermissions();
        $activeTokens = $this->permissionTokenRepository->getActiveToken(Yii::$app->user->id);
        $model = new TokenForm();
        if ($model->load(Yii::$app->request->post())) {
            $currentTime = date('Y-m-d H:i:s'); // Текущая дата и время
            $date = $model->date(new DateTime($currentTime));
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $token = PermissionTokenWork::fill(
                $model->user,
                $model->permission,
                $currentTime,
                $date->format('Y-m-d H:i:s'),
            );
            if (!$this->permissionTokenRepository->isPossibleInsert($model->user, $model->permission)) {
                $this->permissionTokenRepository->save($token);
            }
            sleep(1);
            return $this->redirect(['token',
                'id' => $id,
            ]);
        }
        return $this->render('token', [
            'model' => $model,
            'user' => $user,
            'users' => $users,
            'permissions' => $permissions,
            'activeTokens' => $activeTokens,
        ]);
    }

    public function actionErrors($id)
    {
        $errors = $this->errorService->getErrorsByUser($id);
        $form = new ErrorsForm($errors);
        $user = $this->userRepository->get($id);
        return $this->render('errors', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}