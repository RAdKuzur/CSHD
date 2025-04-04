<?php

use backend\forms\CertificateTemplatesForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model CertificateTemplatesForm */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Редактировать шаблон сертификата: ' . $model->entity->name;
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны сертификатов', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->entity->name, 'url' => ['view', 'id' => $model->entity->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="certificate-templates-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
