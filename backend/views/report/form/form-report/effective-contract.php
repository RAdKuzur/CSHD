<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model \backend\forms\report\ECForm */
$this->title = 'Эффективный контракт';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(['id' => 'effective-contract']);?>
<?= $form->field($model, 'startDate', ['template' => '{label}&nbsp;{input}',
    'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
    'dateFormat' => 'php:Y-m-d',
    'language' => 'ru',
    'options' => [
        'id' => 'date1',
        'placeholder' => '',
        'class'=> 'form-control',
        'autocomplete'=>'off'
    ],
    'clientOptions' => [
        'changeMonth' => true,
        'changeYear' => true,
        'yearRange' => '2000:2100',
    ]])->label('С') ?>
    <?= $form->field($model, 'endDate', [ 'template' => '{label}&nbsp;{input}',
        'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'id' => 'date2',
            'placeholder' => '',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2100',
        ]])->label('По') ?>
    <?= $form->field($model, 'budget')->dropDownList([
        0 => 'Внебюджетная',
        1 => 'Бюджетная'
    ])->label('Основа');?>
<?= Html::submitButton('Составить отчёт', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end();?>
