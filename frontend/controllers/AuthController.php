<?php

namespace frontend\controllers;

use common\models\work\UserWork;
use common\repositories\general\UserRepository;
use frontend\invokables\ForgotPasswordSend;
use frontend\models\auth\ForgotPassword;
use frontend\models\auth\LoginModel;
use frontend\services\AuthService;
use Yii;
use yii\web\Controller;

class AuthController extends Controller
{
    private UserRepository $userRepository;
    private AuthService $service;

    public function __construct(
        $id,
        $module,
        UserRepository $userRepository,
        AuthService $service,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->userRepository = $userRepository;
        $this->service = $service;
    }

    public function actionLogin()
    {
        if (!Yii::$app->rubac->isGuest()) {
            return $this->redirect(['site/index']);
        }

        $model = new LoginModel();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $this->userRepository->findByEmail($model->username);
            /** @var UserWork $user */
            if ($user && $user->validatePassword($model->password)) {
                $duration = 3600 * 12;
                Yii::$app->user->login($user, $duration);
                return
                    Yii::$app->session->get('previous_url') ?
                    $this->redirect(Yii::$app->session->get('previous_url')) :
                    $this->redirect(['site/index']);
            }

            Yii::$app->session->setFlash('danger', 'Неверное имя пользователя и/или пароль');
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionForgotPassword()
    {
        $model = new ForgotPassword();
        if ($model->load(Yii::$app->request->post())) {
            if ($this->service->checkEmail($model->email)) {
                $rawPass = $this->service->resetPassword($model->email);
                $sender = new ForgotPasswordSend($model->email, $rawPass);
                $sender();
                Yii::$app->session->addFlash('success', 'Вам на почту было отправлено письмо с новым паролем (проверьте папку "Спам"!).');
                return $this->redirect(['/auth/login']);
            }
            else
                Yii::$app->session->addFlash('danger', 'Не найден пользователь с таким e-mail.');

        }
        return $this->render('forgot-password', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login']);
    }
}