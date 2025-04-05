<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\TrainingGroup;
use frontend\forms\training_group\TrainingGroupScheduleForm;
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrainingGroupScheduleForm */
/* @var $modelLessons */
/* @var $auditoriums */
/* @var $scheduleTable */
/* @var $buttonsAct */

$this->title = 'Редактирование группы ' . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Группа {$model->number}", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<script>
    function changeScheduleType() {
        const firstDiv = document.getElementById('manual-fields');
        const secondDiv = document.getElementById('auto-fields');

        if (firstDiv.style.display === 'none') {
            firstDiv.style.display = 'block';
            secondDiv.style.display = 'none';
        } else {
            firstDiv.style.display = 'none';
            secondDiv.style.display = 'block';
        }
    }
</script>

<div class="group-create">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
            </div>
        </div>
    </div>

    <div class="training-group-schedule-form field-backing">
        <?php if (strlen($scheduleTable) > 10): ?>
            <?= $scheduleTable; ?>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

        <div class="bordered-div">
            <div style="padding: 0 1em;">
                <?= $form->field($model, 'type')->radioList(
                    array(
                        TrainingGroupScheduleForm::MANUAL => 'Ручное заполнение расписания',
                        TrainingGroupScheduleForm::AUTO => 'Автоматическое расписание по дням'
                    ),
                    [
                        'value' => TrainingGroupScheduleForm::MANUAL,
                        'onchange' => 'changeScheduleType()'
                    ]
                )->label('') ?>
            </div>
        </div>

        <div class="bordered-div">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 4, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelLessons[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'id',
                ],
            ]); ?>

            <div class="container-items"><!-- widgetContainer -->
                <div class="panel-title">
                    <h5 class="panel-title pull-left">Занятие</h5><!-- widgetBody -->
                    <div class="pull-right">
                        <button type="button" class="add-item btn btn-success btn-xs"><span class="glyphicon glyphicon-plus">+</span></button>
                    </div>
                </div>
                <?php foreach ($modelLessons as $i => $modelLesson): ?>
                <div class="item panel panel-default" id = "item"><!-- widgetItem -->
                    <div class="panel-heading">
                        <div class="pull-right">
                            <button type="button" class="remove-item btn btn-warning btn-xs"><span class="glyphicon glyphicon-minus">-</span></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class = "form-label">
                        <div class="panel-body">
                            <div class="row">
                                <div id="manual-fields" style="display: block">
                                    <?= $form->field($modelLesson, "[{$i}]lesson_date")->textInput(
                                        [
                                            'type' => 'date',
                                            'id' => 'inputDate',
                                            'class' => 'form-control inputDateClass'
                                        ]
                                    )->label('Дата занятия') ?>
                                </div>

                                <div id="auto-fields" style="display: none">
                                    <?= $form->field($modelLesson, "[{$i}]autoDate")->checkboxList(
                                        [
                                            1 => 'Каждый понедельник',
                                            2 => 'Каждый вторник',
                                            3 => 'Каждую среду',
                                            4 => 'Каждый четверг',
                                            5 => 'Каждую пятницу',
                                            6 => 'Каждую субботу',
                                            7 => 'Каждое воскресенье'
                                        ],
                                        [
                                            'item' => function ($index, $label, $name, $checked, $value){
                                                if ($checked) {
                                                    $checked = 'checked';
                                                }
                                                return '<label class="checkbox-inline">
                                                            <input class="'.$index.'" type="checkbox" value="' . $value . '" name="' . $name . '" ' . $checked . ' />'.$label.'
                                                        </label><br>';
                                            }
                                        ]
                                    )->label('<div style="padding-bottom: 10px">Периодичность</div>'); ?>
                                </div>

                                <?= $form->field($modelLesson, "[{$i}]lesson_start_time")->textInput(
                                    [
                                        'type' => 'time',
                                        'class' => 'form-control def',
                                        'value' => '08:00',
                                        'min'=>'08:00',
                                        'max'=>'20:00'
                                    ]
                                )->label('Начало занятия') ?>

                                <?= $form->field($modelLesson, "[{$i}]branch")->dropDownList(Yii::$app->branches->getList())->label('Отдел'); ?>

                                <?= $form->field($modelLesson, "[{$i}]auditorium_id")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map($auditoriums, 'id', 'name'),
                                    'size' => Select2::LARGE,
                                    'options' => ['prompt' => '---'],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ])->label('Помещение'); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>


<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= TrainingGroup::tableName() ?>', 'index.php?r=educational/training-group/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>