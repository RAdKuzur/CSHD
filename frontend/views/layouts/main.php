<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php //$this->registerCssFile('@web/vendor/fortawesome/font-awesome/css/all.min.css'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    if (Yii::$app->rubac->isGuest()) {
        $menuItems[] = ['label' => 'Войти', 'url' => ['/auth/login']];
    }
    else {
        $menuItems[] = ['label' => 'Личный кабинет', 'url' => ['/user/lk/info', 'id' => Yii::$app->user->identity->getId()]];
        $menuItems[] = ['label' => 'АНАЛитика', 'url' => ['/analytics/errors', 'id' => Yii::$app->user->identity->getId()]];
        $menuItems[] = ['label' => 'Выйти', 'url' => ['/auth/logout']];
    }

    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    $menuItems = [
        [
            'label' => 'Документооборот',
            'items' => [
                ['label' => 'Входящая документация', 'url' => ['/document/document-in/index']],
                ['label' => 'Исходящая документация', 'url' => ['/document/document-out/index']],
                ['label' => 'Приказы по осн. деятельности', 'url' => ['/order/order-main/index']],
                ['label' => 'Приказы по обр. деятельности', 'url' => ['/order/order-training/index']],
                ['label' => 'Приказы по мероприятиям', 'url' => ['/order/order-event/index']],
                ['label' => 'Положения', 'url' => ['/regulation/regulation/index']],
                ['label' => 'Положения о мероприятиях', 'url' => ['/regulation/regulation-event/index']],
                ['label' => 'Мероприятия', 'url' => ['/event/our-event/index']],
                ['label' => 'Учет достижений в мероприятиях', 'url' => ['/event/foreign-event/index']],
                ['label' => 'Учет ответственности работников', 'url' => ['/responsibility/local-responsibility/index']],
            ],
        ],
        [
            'label' => 'Учебная деятельность',
            'items' => [
                ['label' => 'Образовательные программы', 'url' => ['/educational/training-program/index']],
                ['label' => 'Учебные группы', 'url' => ['/educational/training-group/index']],
                ['label' => 'Генерация сертификатов', 'url' => ['/educational/certificate/index']],
                ['label' => 'Защиты проектов', 'url' => ['/educational/pitch/index']],
            ],
        ],
        [
            'label' => 'Справочники',
            'items' => [
                ['label' => 'Люди', 'url' => ['/dictionaries/people/index']],
                ['label' => 'Организации', 'url' => ['/dictionaries/company/index']],
                ['label' => 'Должности', 'url' => ['/dictionaries/position/index']],
                ['label' => 'Участники деятельности', 'url' => ['/dictionaries/foreign-event-participants/index']],
                ['label' => 'Помещения', 'url' => ['/dictionaries/auditorium/index']],
            ],
        ],
        [
            'label' => Yii::$app->user->isGuest ? 'Профиль' : 'Профиль (' . Yii::$app->user->identity->username . ')',
            'items' => $menuItems,
        ]
    ];

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
        'items' => $menuItems,
    ]);
    /*if (Yii::$app->rubac->isGuest()) {
        echo Html::tag('div',Html::a('Войти',['/auth/login'],['class' => ['btn btn-link login text-decoration-none']]),['class' => ['d-flex']]);
    } else {
        echo Html::beginForm(['/auth/logout'], 'post', ['class' => 'd-flex'])
            . Html::submitButton(
                'Выйти (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout text-decoration-none']
            )
            . Html::endForm();
    }*/
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-start">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
        <p class="float-end"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"  defer></script>
</html>
<?php $this->endPage();
