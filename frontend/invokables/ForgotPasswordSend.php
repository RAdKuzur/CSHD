<?php

namespace frontend\invokables;

use Yii;

class ForgotPasswordSend
{
    private string $email;
    private string $password;

    public function __construct(
        string $email,
        string $password
    )
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function __invoke()
    {
        return Yii::$app->mailer->compose()
            ->setFrom('noreply@schooltech.ru')
            ->setTo($this->email)
            ->setSubject('Восстановление пароля')
            ->setTextBody($this->password)
            ->setHtmlBody('Вы запросили восстановление пароля в системе электронного документооборота ЦСХД (https://index.schooltech.ru/)<br>Ваш новый пароль: '.$this->password.'<br><br>Пожалуйста, обратите внимание, что это сообщение было сгенерировано и отправлено в автоматическом режиме. Не отвечайте на него.')
            ->send();
    }
}