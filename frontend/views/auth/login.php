<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap5\ActiveForm */
/* @var $model \frontend\models\auth\LoginModel */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>

<link rel="stylesheet" href="login.css">

<div class="login-container">
    <div class="login-block1">
        <h1 class="login-header"><?= Html::encode($this->title) ?></h1>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'E-mail', 'class' => 'form-control login-input'])->label(false) ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Пароль', 'class' => 'form-control login-input'])->label(false) ?>

        <div class="forgot-password-block">
            <?= Html::a('Забыли пароль?', \yii\helpers\Url::to(['auth/forgot-password'])) ?>
        </div>

        <div class="form-group submit-login-block">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-login', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="login-block2">
        <div class="text-login-form">
            <h2><b>Добро пожаловать в ЦСХД!</b></h2>
            <hr>
            <p>Для входа используйте свою рабочую почту с доменом @schooltech.ru</p>
        </div>
    </div>
</div>