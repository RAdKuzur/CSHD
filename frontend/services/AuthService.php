<?php

namespace frontend\services;

use common\models\work\UserWork;
use common\repositories\general\UserRepository;
use Yii;

class AuthService
{
    const PASSWORD_GEN_LENGTH = 8;

    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function checkEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }

    public function resetPassword(string $email)
    {
        $rawPass = Yii::$app->security->generateRandomString(self::PASSWORD_GEN_LENGTH);
        $hashPass = Yii::$app->security->generatePasswordHash($rawPass);

        /** @var UserWork $user */
        $user = $this->userRepository->findByEmail($email);
        $user->setPassword($hashPass);
        $this->userRepository->save($user);

        return $rawPass;
    }
}