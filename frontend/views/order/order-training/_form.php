<?php

use frontend\components\GroupParticipantWidget;
use frontend\models\work\order\OrderTrainingWork;
use common\helpers\DateFormatter;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model OrderTrainingWork */
/* @var $people */
/* @var $scanFile */
/* @var $docFiles */
/* @var $groups */
/* @var $groupParticipant */
/* @var $transferGroups */
/* @var $groupCheckOption */
/* @var $groupParticipantOption */
?>

<div class="order-training-form field-backing">
    <?php $form = ActiveForm::begin(); ?>
<?php if($model->id == NULL) {?>
    <?=
        $form->field($model, 'order_date')->widget(DatePicker::class, [
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
        ]])->label('Дата приказа');
    ?>
    <?=
        $form->field($model, 'branch')->dropDownList(
            Yii::$app->branches->getList(),
            [
                'prompt' => '---',
                'id' => 'branch-dropdown' // Добавляем id для доступа в JavaScript
            ]
        )->label('Отдел');
    ?>
    <?=
        // Выпадающий список для выбора кода и описания номенклатуры
        $form->field($model, 'order_number')->dropDownList(
            [], // Сначала оставляем его пустым
            [
                'prompt' => '---',
                'id' => 'order-number-dropdown' // Добавляем id для доступа в JavaScript
            ])->label('Код и описание номенклатуры');
        ?>
        <?php } else {
                echo '<div class="form-group col-xs-4">
                        <label class="control-label">Дата приказа</label>
                        <select class="form-control" disabled>
                            <option selected>'.DateFormatter::format($model->order_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot).'</option>
                        </select>
                    </div>
                    <div class="form-group col-xs-4">
                        <label class="control-label">Код и номенклатура приказа</label>
                        <select class="form-control" disabled>
                            <option selected>'.$model->getOrderType().'</option>
                        </select>
                    </div>
                    <div class="form-group col-xs-4">
                        <label class="control-label">Номер приказа</label>
                        <select class="form-control" disabled>
                            <option selected>'.$model->getNumberPostfix().'</option>
                        </select>
                    </div>
                    <div class="form-group col-xs-4">
                        <label class="control-label">Отдел</label>
                        <select class="form-control" disabled>
                            <option selected>'.Yii::$app->branches->get($model->branch).'</option>
                        </select>
                    </div>';
        }
    ?>

    <div>
        <div id = 'preamble' style="display:none" >
            <?=
               $form->field($model, 'preamble')->radioList([
                    1 => 'По решению аттестационной комиссии/ протоколов жюри/ судейской коллегии/ итоговой диагностической карты',
                    2 => 'В связи с завершением обучения без успешного прохождения итоговой формы контроля',
                    3 => 'По заявлению родителя',
                    4 => 'По соглашению сторон',
                    5 => 'На следующий год обучения',
                    6 => 'С одной ДОП на другую ДОП',
                    7 => 'Из одной учебной группы в другую',
               ])->label('Преамбула');
            ?>
        </div>
    </div>
    <?= GroupParticipantWidget::widget([
        'config' => [
            'groupUrl' => 'get-group-by-branch',
            'participantUrl' => 'get-group-participants-by-branch'
        ],
        'dataProviderGroup' => $groups,
        'model' => $model,
        'dataProviderParticipant' => $groupParticipant,
        'nomenclature' => $model->getNomenclature(),
        'transferGroups' => $transferGroups,
        'groupCheckOption' => $groupCheckOption,
        'groupParticipantOption' => $groupParticipantOption,
    ]);
    ?>
    <?= $form->field($model, 'order_name')->textInput([
            'readonly' => true,
            'id' => 'order-name-label'
    ])->label('Наименование приказа'); ?>
    <div id="bring_id">
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
    <div id="executor_id">
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
    <div>
        <?= $form->field($model, "responsible_id")->widget(Select2::classname(), [
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
    </div>
    <?= $form->field($model, 'key_words')->textInput()->label('Ключевые слова') ?>
    <?= $form->field($model, 'scanFile')->fileInput()->label('Скан документа') ?>
    <?php  if (strlen($scanFile) > 10): ?>
        <?= $scanFile; ?>
    <?php endif; ?>

    <?= $form->field($model, 'docFiles[]')->fileInput(['multiple' => true])->label('Редактируемые документы') ?>

    <?php if (strlen($docFiles) > 10): ?>
        <?= $docFiles; ?>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-primary',
            'onclick' => 'prepareAndSubmit();' // Подготовка скрытых полей перед отправкой
        ]) ?>

        <?php ActiveForm::end(); ?>
</div>
<?php
    if (!is_null($model->preamble)) {
        $this->registerJs("
            if(" . $model->preamble . " >= 1 && " . $model->preamble . " <= 4) {
                $('#preamble').css('display', 'block');
                document.getElementsByName('OrderTrainingWork[preamble]').forEach(radio => {
                    if (['5', '6', '7'].includes(radio.value)) {
                        radio.parentElement.style.display = 'none';
                    }
                });
            }
            if(" . $model->preamble . " > 5) {
                $('#preamble').css('display', 'block');
                document.getElementsByName('OrderTrainingWork[preamble]').forEach(radio => {
                    if (['1', '2', '3', '4'].includes(radio.value)) {
                        radio.parentElement.style.display = 'none';
                    }
                });
            }
                  
        ");
    }
    $this->registerJs("
        $('#branch-dropdown').on('change', function() {
            var branchId = $(this).val();
            $.ajax({
                url: '" . Url::to(['order/order-training/get-list-by-branch']) . "', // Укажите ваш правильный путь к контроллеру
                type: 'GET',
                data: { branch_id: branchId },
                success: function(data) {
                    var options;
                    options = '<option value=\"\">---</option>';
                    $.each(data, function(index, value) {
                        options += '<option value=\"' + index + '\">' + value + '</option>';
                    });
                    $('#order-number-dropdown').html(options); // Обновляем второй выпадающий список
                }
            });
        });
    ");
    $this->registerJs("
        $('#order-number-dropdown').on('change', function() {
            var nomenclature = $('#order-number-dropdown').val();
            var gridView = $('.training-group-participant .grid-view');
            var checkedCheckboxes = $('.group-checkbox:checked');
            var checkedCheckboxAll = $('.select-on-check-all');
            gridView.html(null);
            checkedCheckboxes.prop('checked', false);
            checkedCheckboxAll.prop('checked', false);
            $.ajax({
                url: '" . Url::to(['order/order-training/set-name-order']) . "', // Укажите ваш правильный путь к контроллеру
                type: 'GET',
                data: { 
                    nomenclature: nomenclature
                },
                success: function(data) {
                    console.log($('#order-name-label'));
                    $('#order-name-label').val(data);
                }
            });
        });
    ");
    $this->registerJs("
        $('#order-number-dropdown').on('change', function() {
            var nomenclature = $('#order-number-dropdown').val();
            $.ajax({
                url: '" . Url::to(['order/order-training/set-preamble']) . "', // Укажите ваш правильный путь к контроллеру
                type: 'GET',
                data: { 
                    nomenclature: nomenclature
                },
                success: function(data) {
                    if (data == 2) {
                        $('#preamble').css('display', 'block');
                        document.getElementsByName('OrderTrainingWork[preamble]').forEach(radio => {
                            if (['1', '2', '3', '4'].includes(radio.value)) {
                                radio.parentElement.style.display = 'block';
                            }
                        });
                        document.getElementsByName('OrderTrainingWork[preamble]').forEach(radio => {
                            if (['5', '6', '7'].includes(radio.value)) {
                                radio.parentElement.style.display = 'none';
                            }
                        });
                        
                    }
                    else if (data == 3) {
                        $('#preamble').css('display', 'block');
                        document.getElementsByName('OrderTrainingWork[preamble]').forEach(radio => {
                            if (['1', '2', '3', '4'].includes(radio.value)) {
                                radio.parentElement.style.display = 'none';
                            }
                        });
                         document.getElementsByName('OrderTrainingWork[preamble]').forEach(radio => {
                            if (['5', '6', '7'].includes(radio.value)) {
                                radio.parentElement.style.display = 'block';
                            }
                        });
                    }
                    else if (data == 1) {
                        $('#preamble').css('display', 'none');
                    }
                    document.getElementsByName('OrderTrainingWork[preamble]').forEach(radio => radio.checked = false);
                }
            });
        });
    ");
?>