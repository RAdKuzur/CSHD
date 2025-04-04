<?php

use frontend\models\auth\ForgotPassword;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form ActiveForm */
/* @var $model ForgotPassword */

$this->title = 'Восстановление пароля';
$this->params['breadcrumbs'][] = $this->title;
?>

<link rel="stylesheet" href="login.css">

<div class="login-container">
    <div class="login-block1">
        <h1 class="login-header"><?= Html::encode($this->title) ?></h1>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($model, 'email')->textInput(['placeholder' => 'E-mail', 'class' => 'form-control login-input'])->label(false) ?>

        <div class="form-group submit-login-block">
            <?= Html::submitButton('Восстановить пароль', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="login-block2">
        <div class="text-login-form">
            <h2><b>Форма смены пароля</b></h2>
            <hr>
            <p>Введи e-mail и следуйте инструкциям из электронного письма</p>
        </div>
    </div>
</div>