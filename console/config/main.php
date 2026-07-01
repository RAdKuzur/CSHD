<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
         'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => false,
            'showScriptName' => true,
            'hostInfo' => $params['console.hostInfo'] ?? 'http://localhost/cshd/frontend/web',
            'scriptUrl' => $params['console.scriptUrl'] ?? 'http://localhost/cshd/frontend/web/index.php',
        ],
        'branches' => [
            'class' => 'common\\components\\dictionaries\\base\\BranchDictionary',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.yandex.ru',
                'username' => 'noreply@schooltech.ru',
                'password' => 'noreply0noreply',
                'port' => 465,
                'encryption' => 'ssl',
            ],
        ],
    ],
    'params' => $params,
];
