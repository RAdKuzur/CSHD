<?php

namespace console\controllers;

use common\models\work\UserWork;
use common\repositories\general\UserRepository;
use Yii;
use yii\console\Controller;

class UserController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(
                       $id,
                       $module,
        UserRepository $userRepository,
                       $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userRepository = $userRepository;
    }

    public function actionCreate()
    {
        $splitFio = [];

        while (count($splitFio) < 2) {
            $fio = $this->prompt('Введите ФИО пользователя через пробел (на латинице):');
            $splitFio = explode(' ', $fio);
        }

        $login = $this->prompt('Введите логин пользователя:');
        $email = $this->prompt('Введите email пользователя:');
        $password = $this->prompt('Введите пароль пользователя:');

        $password = Yii::$app->security->generatePasswordHash($password);

        $entityId = $this->userRepository->save(UserWork::fill(
            $splitFio[0],
            $splitFio[1],
            $login,
            $password,
            $email,
            $splitFio[2] ?? null,
        ));

        echo "Пользователь $login успешно создан. ID: $entityId";
    }
}
