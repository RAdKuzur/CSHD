<?php

use common\components\wizards\AlertMessageWizard;
use common\helpers\DateFormatter;
use common\models\scaffold\TrainingGroup;
use frontend\forms\training_group\TrainingGroupBaseForm;
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model TrainingGroupBaseForm */
/* @var $modelTeachers */
/* @var $trainingPrograms */
/* @var $people */
/* @var $photos */
/* @var $presentations */
/* @var $workMaterials */
/* @var $buttonsAct */

if ($model->id) {
    $this->title = 'Редактирование';
    $this->params['breadcrumbs'][] = ['label' => 'Учебные группа', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => "Группа {$model->number}", 'url' => ['view', 'id' => $model->id]];
    $this->params['breadcrumbs'][] = $this->title;
}

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<div class="group-create">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <?php if ($model->id): ?>
        <div class="substrate">
            <h3><?= Html::encode($this->title) ?></h3>
            <div class="flexx space">
                <div class="flexx">
                    <?= $buttonsAct ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="training-group-base-form field-backing">

        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

        <?= $form->field($model, 'branch')->dropDownList(Yii::$app->branches->getList()) ?>

        <?= $form->field($model, 'trainingProgramId')->widget(Select2::classname(), [
            'data' => ArrayHelper::map($trainingPrograms, 'id', 'fullName'),
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
                'widgetContainer' => 'dynamicform_wrapper',
                'widgetBody' => '.container-items',
                'widgetItem' => '.item',
                'limit' => 15,
                'min' => 1,
                'insertButton' => '.add-item', // Плагин сам найдет эту кнопку по классу, где бы она ни была
                'deleteButton' => '.remove-item',
                'model' => $modelTeachers[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'id',
                ],
            ]); ?>

            <div class="bordered-div">
                <div class="panel-title" style="margin-bottom: 15px;">
                    <h5 style="margin: 0; font-weight: bold;">Преподаватели</h5>
                </div>

                <!-- Контейнер с карточками преподавателей -->
                <div class="container-items">
                    <?php foreach ($modelTeachers as $i => $modelTeacher): ?>
                        <div class="item panel panel-default">
                            <div class="panel-heading">
                                <div class="pull-right">
                                    <button type="button" class="remove-item btn btn-warning btn-xs">
                                        <span class="glyphicon glyphicon-minus">-</span>
                                    </button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-label">
                                <div class="panel-body">
                                    <div class="row" style="margin: 0 10px;">
                                        <?= $form->field($modelTeacher, "[{$i}]peopleId")->widget(Select2::classname(), [
                                            'data' => ArrayHelper::map($people, 'id', 'fioPosition'),
                                            'size' => Select2::LARGE,
                                            'options' => ['placeholder' => 'Выберите преподавателя'],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],
                                        ])->label('ФИО'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 15px; padding-top: 10px; border-top: 1px dashed #ddd;">
                    <button type="button" class="add-item btn btn-success btn-sm">
                        <i class="glyphicon glyphicon-plus"></i>+
                    </button>
                </div>
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
                'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
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
                'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
            ]]) ?>

        <div class="bordered-div">
            <div class="checkBlock">
                <?= $form->field($model, 'endLoadOrders')->checkbox() ?>
            </div>
        </div>

        <?= $form->field($model, 'photos[]')->fileInput(['multiple' => true])->label('Фотоматериалы')?>
        <?php if (!empty($photos) && strlen($photos) > 10): ?>
            <?= $photos; ?>
        <?php endif; ?>

        <?= $form->field($model, 'presentations[]')->fileInput(['multiple' => true])->label('Презентационные материалы')?>
        <?php if (!empty($presentations) && strlen($presentations) > 10): ?>
            <?= $presentations; ?>
        <?php endif; ?>

        <?= $form->field($model, 'workMaterials[]')->fileInput(['multiple' => true])->label('Рабочие материалы')?>
        <?php if (!empty($workMaterials) && strlen($workMaterials) > 10): ?>
            <?= $workMaterials; ?>
        <?php endif; ?>

        <div class="form-group" style="margin-top: 20px;">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-lg']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<script>
    window.onload = function() {
        initObjectData(<?= json_encode($model->id) ?>, '<?= TrainingGroup::tableName() ?>', 'index.php?r=educational/training-group/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>