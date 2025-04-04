<?php

use common\components\dictionaries\base\RegulationTypeDictionary;
use common\helpers\DateFormatter;
use frontend\models\work\order\OrderMainWork;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model RegulationWork */
/* @var $form yii\widgets\ActiveForm */
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
            'yearRange' => '2000:2100',
        ]])->label('Дата положения') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

    <?php
    $orders = OrderMainWork::find()->where(['!=', 'order_name', 'Резерв'])->all();
    $items = ArrayHelper::map($orders,'id','fullName');
    $params = [
        'prompt' => '---'
    ];

    echo $form->field($model, "order_id")->dropDownList($items,$params)->label('Приказ');

    ?>

    <?= $form->field($model, 'ped_council_number')->textInput() ?>

    <?= $form->field($model, 'ped_council_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата совета',
            'class'=> 'form-control',
            'autocomplete'=>'off',
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
        ]]) ?>

    <?= $form->field($model, 'par_council_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата собрания',
            'class'=> 'form-control',
            'autocomplete'=>'off',
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
        ]]) ?>



    <?= $form->field($model, 'scanFile')->fileInput()
        ->label('Скан положения')?>

    <?php if (strlen($scanFile) > 10): ?>
        <?= $scanFile; ?>
    <?php endif; ?>

    <?= $form->field($model, 'regulation_type')->hiddenInput(['value' => RegulationTypeDictionary::TYPE_REGULATION])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

