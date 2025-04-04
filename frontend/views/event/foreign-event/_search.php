<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchForeignEvent */
/* @var $form yii\widgets\ActiveForm */
?>

<!--<div class="foreign-event-search">
    <br>
    <h5><b>Введите временные рамки для мероприятия</b></h5>
    <div class="col-xs-4" style="padding-left: 0; width: auto">
        <?php /*$form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); */?>

        <?/*= $form->field($model, 'start_date_search', ['template' => '{label}&nbsp;{input}',
            'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => '',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])->label('С') */?>
    </div>
    <div class="col-xs-4">
        <?/*= $form->field($model, 'finish_date_search', [ 'template' => '{label}&nbsp;{input}',
            'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => '',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])->label('По') */?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="col-xs-5" style="padding-left: 0">
        <?/*= $form->field($model, 'secondnameParticipant')->textInput()->label('Фамилия участника'); */?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="col-xs-5" style="padding-left: 0">
        <?/*= $form->field($model, 'secondnameTeacher')->textInput()->label('Фамилия педагога'); */?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="col-xs-5" style="padding-left: 0">
        <?php
/*        $branchs = \app\models\work\BranchWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($branchs, 'id', 'name');
        $params = [
            'prompt' => ''
        ];
        echo $form->field($model, 'nameBranch')->dropDownList($items,$params)->label('Отдел');

        */?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="form-group">
        <?/*= Html::submitButton('Найти', ['class' => 'btn btn-primary']) */?>
        <?/*= Html::a('Сбросить фильтры', \yii\helpers\Url::to(['foreign-event/index'])) */?>
    </div>

    <?php /*ActiveForm::end(); */?>

</div>-->
