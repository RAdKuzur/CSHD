<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model \backend\forms\report\ECForm */
$this->title = 'Эффективный контракт';
$this->params['breadcrumbs'][] = $this->title;
    // Получаем предыдущий год
    $previousYear = date('Y') - 1;
    $currentYear = date('Y');
    $defaultStartDate = $previousYear . '-01-01';
    $defaultEndDate = $currentYear . '-01-01';

    // Устанавливаем значения по умолчанию для модели, если они еще не установлены
    if (empty($model->startDate)) {
        $model->startDate = $defaultStartDate;
    }
    if (empty($model->endDate)) {
        $model->endDate = $defaultEndDate;
}
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
        <?= $form->field($model, 'budget')->checkboxList([
            0 => 'Внебюджетная',
            1 => 'Бюджетная'
        ])->label('Основа'); ?>
<?= Html::submitButton('Составить отчёт', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end();?>
