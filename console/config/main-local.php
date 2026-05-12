<?php

return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
   'params' => [
        'console.hostInfo' => 'http://localhost/cshd/frontend/web',
        'console.scriptUrl' => 'http://localhost/cshd/frontend/web/index.php',
    ],
];
