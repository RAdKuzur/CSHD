<?php

use backend\forms\report\DodForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model DodForm */
/* @var $form ActiveForm */
?>

<?php
$this->title = 'Отчет 1-ДОД';

// Получаем предыдущий год
$previousYear = date('Y') - 1;
$currentYear = date('Y');
$defaultStartDate = $previousYear . '-01-01';
$defaultEndDate = $previousYear . '-12-31';

// Устанавливаем значения по умолчанию для модели, если они еще не установлены
if (empty($model->startDate)) {
    $model->startDate = $defaultStartDate;
}
if (empty($model->endDate)) {
    $model->endDate = $defaultEndDate;
}
?>

<style>
    .block-report{
        background: #e9e9e9;
        width: auto;
        padding: 10px 10px 0 10px;
        margin-bottom: 20px;
        border-radius: 10px;
        margin-right: 10px;
    }
</style>

<div class="man-hours-report-form">

    <h5><b>Введите период для генерации отчета</b></h5>
    <div class="col-xs-6 block-report">

        <?php $form = ActiveForm::begin(); ?>

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
                        'yearRange' => '2000:' . $currentYear, // Ограничиваем максимальный год текущим
                        'maxDate' => new \yii\web\JsExpression('new Date()'), // Ограничиваем максимальную дату сегодняшним днем
                ]])->label('С') ?>
    </div>

    <div class="col-xs-6 block-report">
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
                        'yearRange' => '2000:' . $currentYear, // Ограничиваем максимальный год текущим
                        'maxDate' => new \yii\web\JsExpression('new Date()'), // Ограничиваем максимальную дату сегодняшним днем
                ]])->label('По') ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div class="form-group">
        <?= Html::submitButton('Скачать отчет', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>
    var elem = document.getElementById('date1');

    elem.onchange = function()
    {
        var startDate = new Date(elem.value);
        var endDate = new Date(startDate.getFullYear(), 11, 31); // 31 декабря того же года

        // Получаем текущую дату
        var currentDate = new Date();

        // Если рассчитанная конечная дата больше текущей даты, устанавливаем текущую дату
        if (endDate > currentDate) {
            endDate = currentDate;
        }

        var elem2 = document.getElementById('date2');
        elem2.value = endDate.getFullYear() + "-" +
            ('0' + (endDate.getMonth() + 1)).slice(-2) + "-" +
            ('0' + endDate.getDate()).slice(-2);
    }

    // Инициализация начальных значений при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        // Если поле даты начала пустое, устанавливаем 01.01 предыдущего года
        if (!elem.value) {
            var currentYear = new Date().getFullYear();
            var previousYear = currentYear - 1;
            elem.value = previousYear + "-01-01";

            // Устанавливаем 31.12 предыдущего года
            var elem2 = document.getElementById('date2');
            elem2.value = previousYear + "-12-31";
        }
    });
</script>