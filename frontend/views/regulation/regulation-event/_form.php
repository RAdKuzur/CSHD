<?php

use common\components\dictionaries\base\RegulationTypeDictionary;
use common\helpers\DateFormatter;
use frontend\models\work\regulation\RegulationWork;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model RegulationWork */
/* @var $form yii\widgets\ActiveForm */
/* @var $ordersList */
/* @var $scanFile */
?>


<div class="regulation-form field-backing">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата документа',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
        ]])->label('Дата положения') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map($ordersList, 'id','fullName'),
        'size' => Select2::LARGE,
        'options' => ['prompt' => '---'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
        ],
    ])->label('Приказ');

    ?>

    <?= $form->field($model, 'scanFile')->fileInput()
        ->label('Скан положения')?>

    <?php if (strlen($scanFile) > 10): ?>
        <?= $scanFile; ?>
    <?php endif; ?>

    <?= $form->field($model, 'regulation_type')->hiddenInput(['value' => RegulationTypeDictionary::TYPE_EVENT])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

