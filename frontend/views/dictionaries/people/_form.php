<?php

use app\components\DynamicWidget;
use common\components\dictionaries\base\BranchDictionary;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use frontend\models\work\general\PeopleWork;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PeopleWork */
/* @var $companies CompanyWork[] */
/* @var $form yii\widgets\ActiveForm */
/* @var $positions PositionWork */
/* @var $branches BranchDictionary */
/* @var $modelPeoplePositionBranch PeoplePositionCompanyBranchWork */

?>

<div class="people-form field-backing">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'surname')->textInput(['maxlength' => true])->label('Фамилия') ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true])->label('Имя') ?>

    <?= $form->field($model, 'patronymic')->textInput(['maxlength' => true])->label('Отчество') ?>

    <?php if (strlen($modelPeoplePositionBranch) > 10): ?>
        <?= $modelPeoplePositionBranch; ?>
    <?php endif; ?>

    <div class="bordered-div">
        <?php DynamicWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper',
            'widgetBody' => '.container-items',
            'widgetItem' => '.item',
            'model' => $model,
            'formId' => 'dynamic-form',
            'formFields' => [
                    'position',
                    'branch'
            ],
        ]);
        ?>

        <div class="container-items">
            <h5 class="panel-title pull-left">Должность</h5><!-- widgetBody -->
            <div class="item panel panel-default" id = "item"><!-- widgetItem -->
                <div class="panel-heading">
                    <div class="clearfix"></div>
                </div>
                <div class = "form-label">
                    <div class="panel-body">
                        <?php
                        $params = [
                            'prompt' => '---',
                            'id' => 'org'
                        ];
                        echo $form->field($model, 'companies[]')->widget(Select2::classname(), [
                            'data' => ArrayHelper::map($companies, 'id', 'name'),
                            'size' => Select2::LARGE,
                            'options' => $params,
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Организация');
                        ?>

                        <?php
                        $params = [
                            'id' => 'position',
                            'prompt' => '---',
                        ];
                        echo $form
                            ->field($model, 'positions[]')
                            ->widget(Select2::classname(), [
                                'data' => ArrayHelper::map($positions, 'id', 'name'),
                                'size' => Select2::LARGE,
                                'options' => $params,
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])
                            ->label('Должность');
                        ?>

                        <?php
                        $params = [
                            'id' => 'branch',
                            'prompt' => '---',
                        ];
                        echo $form
                            ->field($model, 'branches[]')
                            ->dropDownList(Yii::$app->branches->getList(), ['prompt' => '---'])
                            ->label('Отдел(при наличии)');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        DynamicWidget::end()
        ?>
    </div>
    <div id="orghid" <?= !$model->inMainCompany() ? 'hidden' : '' ?>>

        <?= $form->field($model, 'short')->textInput(['maxlength' => true])->label('ФИО код') ?>
        <?= $form->field($model, 'genitive_surname')->textInput(['maxlength' => true])->label('Фамилия в обороте "назначить <i>кого</i>"') ?>
        <?= $form->field($model, 'branch')->dropDownList(Yii::$app->branches->getList(), ['prompt' => '---'])->label('Отдел по трудовому договору'); ?>
        <?= $form->field($model, 'birthdate')->widget(DatePicker::class, [
            'dateFormat' => 'php:d.m.Y',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '1900:2100',
            ]])->label('Дата рождения') ?>

        <div class="bordered-div checkBlock">
            <?= $form->field($model, 'sex')->radioList(array(
                0 => 'Мужской',
                1 => 'Женский',
                2 => 'Другое'
            ), ['value' => $model->sex, 'class' => 'i-checks'])->label('Пол') ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>
    $("#org").change(function() {
        if (this.options[this.selectedIndex].value === `1`)
            $("#orghid").removeAttr("hidden");
        else
            $("#orghid").attr("hidden", "true");
    });
</script>