<?php

namespace common\invokables;

use common\helpers\html\HtmlBuilder;
use common\models\work\ErrorsWork;
use Yii;

class ErrorsSender
{
    private string $email;

    /** @var ErrorsWork[] $errors */
    private array $errors;

    public function __construct(
        string $email,
        array $errors
    )
    {
        $this->email = $email;
        $this->errors = $errors;
    }

    public function __invoke()
    {
        $string = 'Еженедельная сводка об ошибках в ЦСХД. Внимание, в данной сводке выводятся только критические ошибки!' . '<br><br><div style="max-width: 800px;">';
        $string .= HtmlBuilder::createErrorsTable($this->errors) . '</div>';   // тут будет лежать всё то, что отправится пользователю
        $string .= '<br><br> Чтобы узнать больше перейдите на сайт ЦСХД: https://index.schooltech.ru/';
        $string .= '<br>---------------------------------------------------------------------------';
        return Yii::$app->mailer->compose()
            ->setFrom('noreply@schooltech.ru')
            ->setTo($this->email)
            ->setSubject('Cводка критических ошибок по ЦСХД')
            ->setHtmlBody( $string . '<br><br>Пожалуйста, обратите внимание, что это сообщение было сгенерировано и отправлено в автоматическом режиме. Не отвечайте на него.')
            ->send();
    }
}