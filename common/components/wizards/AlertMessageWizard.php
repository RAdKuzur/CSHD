<?php

namespace common\components\wizards;

use common\helpers\html\HtmlBuilder;use Yii;class AlertMessageWizard
{
    public static function showRedisConnectMessage()
    {
        if (!Yii::$app->redis->isConnected()) {
            return HtmlBuilder::createMessage(
                HtmlBuilder::TYPE_WARNING,
                'Отключена система блокировки ресурсов. Будьте оперативны при заполнении карточек и не оставляйте надолго открытой форму редактирования',
                'Внимание!'
            );
        }
        else {
            return HtmlBuilder::createMessage(
                HtmlBuilder::TYPE_INFO,
                'Работает система блокировки ресурсов. Данный ресурс сейчас заблокирован для других пользователей, пока Вы не завершите редактирование<hr>
                    <i>Вас автоматически перенаправит на страницу просмотра после <b>10 минут бездействия</b>. Внесенные изменения не будут применены. Будьте внимательны!</i>'
            );
        }
    }
}