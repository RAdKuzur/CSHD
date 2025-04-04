<?php

use frontend\models\work\educational\training_program\ThematicPlanWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ThematicPlanWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temporary-journal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'theme')->textInput()->label('Тема') ?>

    <?= $form->field($model, 'control_type')->dropDownList(Yii::$app->controlType->getList(), ['prompt' => '---'])->label('Форма контроля'); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>