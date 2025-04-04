<?php

use frontend\models\work\dictionaries\AuditoriumWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model AuditoriumWork */
/* @var $form yii\widgets\ActiveForm */
/* @var $otherFiles */
?>

<div class="auditorium-form">

    <?php $form = ActiveForm::begin(['id' => 'some-form1']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'square')->textInput() ?>

    <?= $form->field($model, 'text')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_education')->checkbox(['id' => 'org', 'onclick' => "checkEdu()"]) ?>

    <?php
    if ($model->is_education === 1) {
        echo '<div id="orghid">';
    }
    else {
        echo '<div id="orghid" hidden>';
    }
    ?>

    <?= $form->field($model, 'capacity')->textInput() ?>
    <?= $form->field($model, 'auditorium_type')->dropDownList(Yii::$app->auditoriumType->getList(), ['prompt' => '---']); ?>

    </div>

    <?= $form->field($model, 'branch')->dropDownList(Yii::$app->branches->getList()); ?>
    <?= $form->field($model, 'window_count')->textInput(['type' => 'number', 'style' => 'width: 40%']) ?>
    <?= $form->field($model, 'include_square')->checkbox() ?>
    <?= $form->field($model, 'filesList[]')->fileInput(['multiple' => true])->label('Файлы') ?>

    <?php if (strlen($otherFiles) > 10): ?>
        <?= $otherFiles; ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>
    $('#org').change(function()
    {
        if (this.checked === true)
            $("#orghid").removeAttr("hidden");
        else
            $("#orghid").attr("hidden", "true");
    });
</script>