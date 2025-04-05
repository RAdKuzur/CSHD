<?php

namespace common\components\traits;

use Yii;
use yii\base\Action;

trait AccessControl
{
    /**
     * Метод для вызова в beforeAction, реализует проверку на права доступа к экшну
     *
     * @param Action $action
     * @return array ['url' => 'адрес для перенаправления/пустой, если доступ разрешен', 'status' => 'статус разрешения редиректа']
     */
    public function checkActionAccess(Action $action)
    {
        Yii::$app->session->set('previous_url', Yii::$app->request->url);

        if (Yii::$app->rubac->isGuest()) {
            return [
                'url' => ['/auth/login'],
                'status' => true
            ];
        }

        if (!Yii::$app->rubac->checkUserAccess(Yii::$app->rubac->authId(), get_class(Yii::$app->controller), $action)) {
            Yii::$app->session->setFlash('error', 'У Вас недостаточно прав. Обратитесь к администратору для получения доступа');
            return [
                'url' => Yii::$app->request->referrer,
                'status' => false
            ];
        }

        return [
            'url' => '',
            'status' => true
        ];
    }
}