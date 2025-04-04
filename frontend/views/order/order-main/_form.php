<?php

use common\components\dictionaries\base\NomenclatureDictionary;
use common\helpers\DateFormatter;
use frontend\models\work\order\OrderMainWork;
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model OrderMainWork */
/* @var $form yii\widgets\ActiveForm */
/* @var $people */

/* @var $modelExpire */
/* @var $orders */
/* @var $regulations */
/* @var $modelChangedDocuments */
/* @var $scanFile */
/* @var $docFiles */
?>

<div class="order-main-form field-backing">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?= $form->field($model, 'order_date')->widget(DatePicker::class, [
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
            'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
        ]])->label('Дата приказа') ?>

    <div id="archive-2" class="col-xs-4">
        <label class="control-label">Код и номенклатура приказа</label>
        <select class="form-control" disabled>
            <option selected><?= NomenclatureDictionary::ADMIN_ORDER ?> Приказы директора по основной деятельности</option>
        </select>
    </div>

    <?php   if($model->id == NULL){?>
    <?= $form->field($model, 'archive')->checkbox(['id' => 'study_type', 'onchange' => 'checkArchive()']) ?>
    <div id="archive" class="col-xs-4"<?= $model->study_type == 0 ? 'hidden' : '' ?>>
        <?= $form->field($model, 'order_number')->textInput()->label('Архивный номер') ?>
    </div>
    <?php  } ?>
    <?= $form->field($model, 'order_name')->textInput()->label('Наименование приказа') ?>
    <div id="bring">
        <?php
        $params = [
            'id' => 'bring',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'bring_id')
            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
            ->label('Проект вносит');
        ?>
    </div>
    <div id="executor">
        <?php
        $params = [
            'id' => 'executor',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'executor_id')
            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
            ->label('Кто исполняет');
        ?>

    </div>
    <?= $form->field($model, "responsible_id")->widget(Select2::class, [
        'data' => ArrayHelper::map($people,'id','fullFio'),
        'size' => Select2::LARGE,
        'options' => [
            'prompt' => 'Выберите ответственного' ,
            'multiple' => true
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label('ФИО ответственного'); ?>
    <?php if (strlen($modelChangedDocuments) > 10): ?>
        <?= $modelChangedDocuments; ?>
    <?php endif; ?>
    <div class="bordered-div">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper_act', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items-act', // required: css class selector
            'widgetItem' => '.item-act', // required: css class
            'limit' => 20, // the maximum times, an element can be cloned (default 999)
            'min' => 1,
            'insertButton' => '.add-item-act', // css class
            'deleteButton' => '.remove-item-act', // css class
            'model' => $modelExpire[0],
            'formId' => 'dynamic-form',
            'formFields' => [
                'full_name',
            ],
        ]); ?>
        <div class="container-items-act"><!-- widgetContainer -->
            <?php foreach ($modelExpire as $i => $expire): ?>
                <div class="item-act panel panel-default"><!-- widgetBody -->
                    <div class="panel-heading">
                        <div class="pull-right">
                            <button type="button" class="add-item-act btn btn-success btn-xs" ><i class="glyphicon glyphicon-plus">+</i></button>
                            <button type="button" class="remove-item-act btn btn-warning btn-xs"><i class="glyphicon glyphicon-minus">-</i></button>
                        </div>
                        <div class="clearfix"></div>
                        <div class="panel-body">
                            <?php
                            $params = [
                                'class' => 'form-control pos',
                                'prompt' => '---',
                            ];
                            echo $form
                                ->field($expire, "[{$i}]expireOrderId")
                                ->dropDownList(ArrayHelper::map($orders, 'id', 'fullOrderName'), $params)
                                ->label('Приказ');
                            echo $form
                                ->field($expire, "[{$i}]expireRegulationId")
                                ->dropDownList(ArrayHelper::map($regulations, 'id', 'name'), $params)
                                ->label('Положение');
                            echo $form
                                ->field($expire, "[{$i}]expireType") // Используем обычный статус
                                ->dropDownList([
                                    '1' => 'Утратило силу',
                                    '2' => 'Изменено',
                                ], $params)
                                ->label('Статус');
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php DynamicFormWidget::end(); ?>
    </div>
    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true])->label('Ключевые слова') ?>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <?= $form->field($model, 'scanFile')->fileInput()->label('Скан документа') ?>
    <?php if (strlen($scanFile) > 10): ?>
        <?= $scanFile; ?>
    <?php endif; ?>

    <?= $form->field($model, 'docFiles[]')->fileInput(['multiple' => true])->label('Редактируемые документы') ?>

    <?php if (strlen($docFiles) > 10): ?>
        <?= $docFiles; ?>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
    function checkArchive() {
        var chkBox = document.getElementById('study_type'); // Получаем чекбокс по ID
        // Если чекбокс отмечен
        if (chkBox.checked) {
            // Показываем элемент, убирая атрибут hidden
            $("#archive").prop("hidden", false);
            $("#archive-2").prop("hidden", true);
        } else {
            // Скрываем элемент, добавляя атрибут hidden
            $("#archive").prop("hidden", true);
            $("#archive-2").prop("hidden", false);
        }
    }
</script>










