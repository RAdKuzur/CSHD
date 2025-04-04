<?php

use backend\forms\report\ForeignEventReportForm;
use backend\services\report\ReportFacade;
use frontend\models\work\event\ParticipantAchievementWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ForeignEventReportForm */
/* @var $form ActiveForm */
?>

<?php
$this->title = 'Генерация отчета по мероприятиям';
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
                'placeholder' => '',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])->label('С') ?>
    </div>

    <div class="col-xs-6 block-report">
        <?= $form->field($model, 'endDate', [ 'template' => '{label}&nbsp;{input}',
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
                'yearRange' => '2000:2100',
            ]])->label('По') ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="col-xs-8 block-report">
        <?= $form->field($model, 'branches')->checkboxList(Yii::$app->branches->getOnlyEducational(), ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="branch-'. $index .'">
                        <input id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
        }])->label('Отдел');
        ?>
    </div>
    <div class="col-xs-8 block-report">
        <?= $form->field($model, 'focuses')->checkboxList(Yii::$app->focus->getList(), ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="focus-'. $index .'">
                        <input id="focus-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
        }])->label('Направленность');
        ?>
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div class="col-xs-8 block-report">
        <?= $form->field($model, 'allowRemotes')->checkboxList(Yii::$app->allowRemote->getList(), ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="allow-'. $index .'">
                        <input id="allow-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
        }])->label('Форма реализации');
        ?>
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div class="col-xs-8 block-report">
        <?php
        $arr = [ParticipantAchievementWork::TYPE_WINNER => 'Победители', ParticipantAchievementWork::TYPE_PRIZE => 'Призеры'];
        echo $form->field($model, 'prizeTypes')->checkboxList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="prize-'. $index .'">
                        <input id="prize-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
        }])->label('Победители и призеры');
        ?>
    </div>

    <div class="col-xs-8 block-report">
        <?= $form->field($model, 'levels')->checkboxList(Yii::$app->eventLevel->getList(), ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="level-'. $index .'">
                        <input id="level-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
        }])->label('Уровень мероприятия');
        ?>
    </div>

    <div class="col-xs-8 block-report" id="mode">
        <?php
        $arr = [ReportFacade::MODE_PURE => 'Только отчетные данные', ReportFacade::MODE_DEBUG => 'Отчетные данные и детализация в csv-файле'];
        echo $form->field($model, 'mode')->radioList($arr, [
            'item' => function($index, $label, $name, $checked, $value)
            {
                return '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black">
                            <label for="mode-'. $index .'">
                                <input onchange="showHours()" id="mode-'. $index .'" name="'. $name .'" type="radio" '. $checked .' value="'. $value .'">
                                <span></span>
                                '. $label .'
                            </label>
                        </div>';
            }
        ])->label('Режим формирования отчета');
        ?>
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div class="form-group">
        <?= Html::submitButton('Генерировать отчет', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>

</script>