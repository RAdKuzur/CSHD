<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\TrainingGroup;
use frontend\forms\training_group\TrainingGroupParticipantForm;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
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
/* @var $buttonsAct */

$this->title = 'Редактирование ' . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Группа {$model->number}", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

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

    <div class="training-group-participant-form field-backing">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

        <?= $form->field($model, 'participantFile')->fileInput(['multiply' => false])->label('Загрузить учащихся из файла') ?>

        <div class="bordered-div">
            <div class="">
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
                <div class="panel-title">
                    <h5 class="panel-title pull-left">Учащиеся</h5><!-- widgetBody -->
                    <div class="pull-right">
                        <button type="button" class="add-item btn btn-success btn-xs"><span class="glyphicon glyphicon-plus">+</span></button>
                    </div>
                </div>
                <?php foreach ($modelChilds as $i => $modelChild): ?>
                    <div class="item panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <div class="pull-right">
                                <button type="button" class="remove-item btn btn-warning btn-xs"><span class="glyphicon glyphicon-minus">-</span></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <?= $form->field($modelChild, "[{$i}]id")->hiddenInput()->label(false) ?>
                                <div class="flexx">
                                    <div class="flx1">
                                        <?= $form->field($modelChild, "[{$i}]participant_id")->widget(Select2::classname(), [
                                            'data' => ArrayHelper::map($childs, 'id', function (ForeignEventParticipantsWork $model) {
                                                return $model->getFullFioWithBirthdate();
                                            }),
                                            'size' => Select2::LARGE,
                                            'options' => ['prompt' => 'Выберите ученика'],
                                            'pluginOptions' => [
                                                'allowClear' => true
                                            ],
                                        ])->label('ФИО учащегося'); ?>
                                    </div>
                                    <div class="flx1">
                                        <?= $form->field($modelChild, "[{$i}]send_method")->dropDownList(
                                            Yii::$app->sendMethods->getList(), ['prompt' => '---']
                                        )->label('Способ доставки сертификата'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
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
