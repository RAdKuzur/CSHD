<?php

use backend\forms\report\ManHoursReportForm;
use backend\services\report\ReportFacade;
use common\helpers\DateFormatter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ManHoursReportForm */
/* @var $form ActiveForm */
?>

<?php
$this->title = 'Генерация отчета по обучающимся';
?>

<style>
    .block-report{
        background: #e9e9e9;
        margin-bottom: 1rem;
        border-radius: 5px;
        margin-right: 2%;
        padding: 0.5rem 0.5rem 1px 0.5rem;
    }

    .big-block-report {
        width: 98%
    }

    .float-block-report {
        float: left;
        width: 48%;
    }

    .float-container-report::after {
        content: "";
        display: table;
        clear: both;
    }
</style>

<div class="man-hours-report-form">

    <h5><b>Введите период для генерации отчета</b></h5>
    <div class="float-container-report">
        <div class="col-xs-4 block-report float-block-report">

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
                    'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
                ]])->label('<b>С</b>') ?>
        </div>

        <div class="col-xs-4 block-report float-block-report">
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
                    'yearRange' => '2000:2050',
                ]])->label('<b>По</b>') ?>
        </div>
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div style="max-width: 100%">
        <div class="float-container-report">
            <div class="col-xs-8 block-report float-block-report">
                <?= $form->field($model, 'branch')->checkboxList(Yii::$app->branches->getOnlyEducational(), ['item' => function ($index, $label, $name, $checked, $value) {
                    return
                        '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="branch-'. $index .'">
                            <input id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
                }])->label('<b>Отдел</b>');
                ?>
            </div>
            <div class="col-xs-8 block-report float-block-report">
                <?= $form->field($model, 'focus')->checkboxList(Yii::$app->focus->getList(), ['item' => function ($index, $label, $name, $checked, $value) {
                    return
                        '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="focus-'. $index .'">
                            <input id="focus-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
                }])->label('<b>Направленность</b>');
                ?>
            </div>
        </div>

        <div class="float-container-report">
            <div class="col-xs-8 block-report float-block-report">
                <?= $form->field($model, 'allowRemote')->checkboxList(Yii::$app->allowRemote->getList(), ['item' => function ($index, $label, $name, $checked, $value) {
                    return
                        '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="allow-'. $index .'">
                            <input id="allow-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
                }])->label('<b>Форма реализации</b>');
                ?>
            </div>
            <div class="col-xs-8 block-report float-block-report">
                <?php
                $arr = ['1' => 'Бюджет', '0' => 'Внебюджет'];
                echo $form->field($model, 'budget')->checkboxList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
                    return
                        '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="budget-'. $index .'">
                            <input id="budget-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
                }])->label('<b>Основа</b>');
                ?>
            </div>
        </div>

    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div style="min-width: 100%">
        <div class="col-xs-8 block-report big-block-report">
            <?= $form->field($model, 'type')->checkboxList(
                [
                    ManHoursReportForm::MAN_HOURS_REPORT => 'Кол-ву человеко-часов',
                    ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN => 'Кол-ву обучающихся, начавших обучение до начала заданного периода и завершивших обучение в заданный период',
                    ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER => 'Кол-ву обучающихся, начавших обучение в заданный период и завершивших обучение после окончания заданного периода',
                    ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN => 'Кол-ву обучающихся, начавших обучение после начала заданного периода и завершивших обучение до окончания заданного периода',
                    ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER => 'Кол-ву обучающихся, начавших обучение до начала заданного периода и завершивших обучение после окончания заданного периода'
                ],
                [
                    'item' => function($index, $label, $name, $checked, $value)
                    {
                        return '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black">
                                    <label for="interview-'. $index .'">
                                        <input onchange="showHours()" id="interview-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                                        <span></span>
                                        '. $label .'
                                    </label>
                                </div>';
                    }
                ])->label('<b>Сгенерировать отчет по</b>'); ?>
        </div>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="float-container-report">
        <div class="col-xs-8 block-report float-block-report" id="unic" style="display: none">
            <?php
            $arr = [ManHoursReportForm::PARTICIPANTS_ALL => 'Все обучающиеся', ManHoursReportForm::PARTICIPANTS_UNIQUE => 'Уникальные обучающиеся'];
            echo $form->field($model, 'unic')->radioList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
                return
                    '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="unic-'. $index .'">
                        <input id="unic-'. $index .'" name="'. $name .'" type="radio" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
            }])->label('<b>Метод подсчета обучающихся</b>');
            ?>
        </div>
        <div class="col-xs-8 block-report float-block-report" id="hours" style="display: none">
            <?php
            $arr = [ManHoursReportForm::MAN_HOURS_FAIR => 'Метод, учитывающий неявки', ManHoursReportForm::MAN_HOURS_ALL => 'Метод, игнорирующий неявки'];
            echo $form->field($model, 'method')->radioList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
                return
                    '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="methods-'. $index .'">
                        <input id="methods-'. $index .'" name="'. $name .'" type="radio" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
            }])->label('<b>Метод подсчета человеко-часов</b>');
            ?>
        </div>
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>


    <div class="col-xs-8 block-report" id="teachers" style="display: none">
        <?= $form->field($model, 'teacher')->dropDownList(
                ArrayHelper::map($model->teachers, 'id', 'fullName'),
                ['prompt' => 'Все педагоги']
        )->label('<b>Педагог</b>');
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
        ])->label('<b>Режим формирования отчета</b>');
        ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>


    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="form-group">
        <?= Html::submitButton('Генерировать отчет', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>
    function showHours()
    {
        var elem = document.getElementById('interview-0');
        var hour = document.getElementById('hours');
        var teach = document.getElementById('teachers');
        if (elem.checked) { hour.style.display = "block"; teach.style.display = "block"; }
        else { hour.style.display = "none"; teach.style.display = "none"; }

        var elem1 = document.getElementById('interview-1');
        var elem2 = document.getElementById('interview-2');
        var elem3 = document.getElementById('interview-3');
        var unic = document.getElementById('unic');
        if (elem1.checked || elem2.checked || elem3.checked) { unic.style.display = "block"; }
        else { unic.style.display = "none"; }
    }

</script>