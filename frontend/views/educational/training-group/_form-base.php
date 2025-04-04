<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\TrainingGroup;
use frontend\forms\training_group\TrainingGroupBaseForm;
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrainingGroupBaseForm */
/* @var $modelTeachers */
/* @var $trainingPrograms */
/* @var $people */
/* @var $photos */
/* @var $presentations */
/* @var $workMaterials */

$this->title = 'Редактирование';
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Группа {$model->number}", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<div class="group-create">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('Основная информация', Url::to(['educational/training-group/base-form', 'id' => $model->id]), ['class' => 'btn btn-success']) ?>
    <?= Html::a('Список учеников', Url::to(['educational/training-group/participant-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Расписание', Url::to(['educational/training-group/schedule-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Сведения о защите работ', Url::to(['educational/training-group/pitch-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>

    <div class="training-group-base-form">

        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
        <?= $form->field($model, 'branch')->dropDownList(Yii::$app->branches->getList()) ?>
        <?= $form->field($model, 'trainingProgramId')->widget(Select2::classname(), [
            'data' => ArrayHelper::map($trainingPrograms,'id','name'),
            'size' => Select2::LARGE,
            'options' => ['prompt' => 'Выберите образовательную программу'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
        <?= $form->field($model, 'budget')->checkbox() ?>
        <?= $form->field($model, 'network')->checkbox() ?>

        <div class="panel-body">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 10, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelTeachers[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'id',
                ],
            ]); ?>

            <div class="container-items"><!-- widgetContainer -->
                <?php foreach ($modelTeachers as $i => $modelTeacher): ?>
                    <div class="item panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <h3 class="panel-title pull-left">ФИО педагога</h3>
                            <div class="pull-right">
                                <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <?= $form->field($modelTeacher, "[{$i}]peopleId")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map($people,'id','fullFio'),
                                    'size' => Select2::LARGE,
                                    'options' => ['prompt' => 'Выберите преподавателя'],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ])->label('ФИО'); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>

        <?= $form->field($model, 'startDate')->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:d.m.Y',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата начала занятий',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) ?>

        <?= $form->field($model, 'endDate')->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:d.m.Y',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата окончания занятий',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]]) ?>
        <?= $form->field($model, 'endLoadOrders')->checkbox() ?>

        <?= $form->field($model, 'photos[]')->fileInput(['multiple' => true])->label('Фотоматериалы')?>
        <?php if (strlen($photos) > 10): ?>
            <?= $photos; ?>
        <?php endif; ?>

        <?= $form->field($model, 'presentations[]')->fileInput(['multiple' => true])->label('Презентационные материалы')?>
        <?php if (strlen($presentations) > 10): ?>
            <?= $presentations; ?>
        <?php endif; ?>

        <?= $form->field($model, 'workMaterials[]')->fileInput(['multiple' => true])->label('Рабочие материалы')?>
        <?php if (strlen($workMaterials) > 10): ?>
            <?= $workMaterials; ?>
        <?php endif; ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= TrainingGroup::tableName() ?>', 'index.php?r=educational/training-group/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>