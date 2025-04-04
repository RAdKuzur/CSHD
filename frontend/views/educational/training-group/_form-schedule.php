<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\TrainingGroup;
use frontend\forms\training_group\TrainingGroupScheduleForm;
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrainingGroupScheduleForm */
/* @var $modelLessons */
/* @var $auditoriums */
/* @var $scheduleTable */

$this->title = 'Редактирование';
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

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('Основная информация', Url::to(['educational/training-group/base-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Список учеников', Url::to(['educational/training-group/participant-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Расписание', Url::to(['educational/training-group/schedule-form', 'id' => $model->id]), ['class' => 'btn btn-success']) ?>
    <?= Html::a('Сведения о защите работ', Url::to(['educational/training-group/pitch-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>

    <?php if (strlen($scheduleTable) > 10): ?>
        <?= $scheduleTable; ?>
    <?php endif; ?>

    <div class="training-group-schedule-form">
        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

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

        <div class="panel-body">
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
                <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                <?php foreach ($modelLessons as $i => $modelLesson): ?>
                    <div class="item panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <h3 class="panel-title pull-left">Занятие</h3>
                            <div class="pull-right">
                                <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
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

                                <?= $form->field($modelLesson, "[{$i}]branch")->dropDownList(Yii::$app->branches->getList()); ?>

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
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
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