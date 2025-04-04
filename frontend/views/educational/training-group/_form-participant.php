<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\TrainingGroup;
use frontend\forms\training_group\TrainingGroupParticipantForm;
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrainingGroupParticipantForm */
/* @var $modelChilds */
/* @var $childs */

$this->title = 'Редактирование';
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Группа {$model->number}", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<div class="event-create">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('Основная информация', Url::to(['educational/training-group/base-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Список учеников', Url::to(['educational/training-group/participant-form', 'id' => $model->id]), ['class' => 'btn btn-success']) ?>
    <?= Html::a('Расписание', Url::to(['educational/training-group/schedule-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Сведения о защите работ', Url::to(['educational/training-group/pitch-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>

<?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="panel-body">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => 100, // the maximum times, an element can be cloned (default 999)
            'min' => 0,
            'insertButton' => '.add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => $modelChilds[0],
            'formId' => 'dynamic-form',
            'formFields' => [
                'id',
                'participant_id',
                'send_method'
            ],
        ]); ?>

        <div class="container-items"><!-- widgetContainer -->
            <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
            <?php foreach ($modelChilds as $i => $modelChild): ?>
                <div class="item panel panel-default"><!-- widgetBody -->
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">Учащийся</h3>
                        <div class="pull-right">
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <?= $form->field($modelChild, "[{$i}]id")->hiddenInput()->label(false) ?>

                            <?= $form->field($modelChild, "[{$i}]participant_id")->widget(Select2::classname(), [
                                'data' => ArrayHelper::map($childs, 'id', 'fullFio'),
                                'size' => Select2::LARGE,
                                'options' => ['prompt' => 'Выберите ученика'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label('ФИО учащегося'); ?>

                            <?= $form->field($modelChild, "[{$i}]send_method")->dropDownList(
                                Yii::$app->sendMethods->getList(), ['prompt' => '---']
                            )->label('Способ доставки сертификата'); ?>
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

<?php ActiveForm::end(); ?>

<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= TrainingGroup::tableName() ?>', 'index.php?r=educational/training-group/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>
