<?php

use frontend\forms\participants\MergeParticipantForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\jui\AutoComplete;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model MergeParticipantForm */

$this->title = 'Слияние участников деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Слияние', 'url' => ['merge-participant']];
?>
<style>
    .block-report{
        background: #e9e9e9;
        width: 45%;
        padding: 10px 10px 0 10px;
        margin-bottom: 20px;
        border-radius: 10px;
        margin-right: 10px;
    }
    .badge {
        padding: 3px 9px 4px;
        font-size: 13px;
        font-weight: bold;
        white-space: nowrap;
        color: #ffffff;
        background-color: #999999;
        -webkit-border-radius: 9px;
        -moz-border-radius: 9px;
        border-radius: 9px;
    }
    .badge:hover {
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }
    .badge-error {
        background-color: #b94a48;
    }
    .badge-error:hover {
        background-color: #953b39;
    }
    .badge-success {
        background-color: #468847;
    }
    .badge-success:hover {
        background-color: #356635;
    }
</style>

<div class="man-hours-report-form">

    <h5><b>Выберите двух участников деятельности</b></h5>
    <div class="col-xs-6 block-report">

        <?php $form = ActiveForm::begin(); ?>

        <?php /*= $form->field($model, 'fio1')->widget(
            AutoComplete::className(), [
            'clientOptions' => [
                'source' => $model->data->participants,

                'select' => new JsExpression("function( event, ui ) {
                    $('#participant_id1').val(ui.item.id); //#memberssearch-family_name_id is the id of hiddenInput.
                    CheckFieldsFill();
                 }"),
            ],
            'options'=>[
                'class'=>'form-control on',
            ]
        ])->label('ФИО участника деятельности №1'); */?>

        <?= $form->field($model, 'fio1')->widget(Select2::classname(), [
            'data' => ArrayHelper::map($model->data->participants, 'id', 'label'),
            'size' => Select2::LARGE,
            'pluginOptions' => [
                'allowClear' => true,
                'templateSelection' => new JsExpression("function( state ) {
                    $('#participant_id1').val(state.id);
                    CheckFieldsFill();
                    return state.text;
                 }"),
            ],
            'options' => [
                'prompt' => '---',
                'class'=>'form-control on',

            ],
        ])->label('ФИО участника деятельности №1'); ?>

        <?= $form->field($model, 'id1')->hiddenInput(['class' => 'part', 'id' => 'participant_id1', 'name' => 'MergeParticipantForm[id1]'])->label(false); ?>
    </div>

    <div class="col-xs-6 block-report">
        <?php /*= $form->field($model, 'fio2')->widget(
            AutoComplete::className(), [
            'clientOptions' => [
                'source' => $model->data->participants,
                'select' => new JsExpression("function( event, ui ) {
                    let e1 = document.getElementById('participant_id1');
                    let e2 = document.getElementById('participant_id2');
                    console.log(e1);
                    console.log(e2);

                    
                 }"),

            ],
            'options'=>[
                'class'=>'form-control on',
            ]
        ])->label('ФИО участника деятельности №2'); */?>

        <?= $form->field($model, 'fio2')->widget(Select2::classname(), [
            'data' => ArrayHelper::map($model->data->participants, 'id', 'label'),
            'size' => Select2::LARGE,
            'pluginOptions' => [
                'allowClear' => true,
                'templateSelection' => new JsExpression("function( state ) {
                    let e1 = document.getElementById('participant_id1');
                    let e2 = document.getElementById('participant_id2');

                    $('#participant_id2').val(state.id);
                    $.get(
                            \"" . Url::toRoute('info') . "\", 
                            {id1: e1.value, id2: e2.value},
                        function(res){
                            let elem = document.getElementById('commonBlock');
                            elem.innerHTML = res;
                        }
                    );
                    CheckFieldsFill();
                    return state.text;
                 }"),
            ],
            'options' => [
                'prompt' => '---',
                'class'=>'form-control on',
            ],
        ])->label('ФИО участника деятельности №2'); ?>

        <?= $form->field($model, 'id2')->hiddenInput(['class' => 'part', 'id' => 'participant_id2', 'name' => 'MergeParticipantForm[id2]'])->label(false); ?>

    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div id="commonBlock" style="display: none">
    </div>
    
    <div id="editBlock" style="display: none; width: 91%;">
        <?= $form->field($model->editModel, 'surname')->textInput()->label('Фамилия') ?>
        <?= $form->field($model->editModel, 'firstname')->textInput()->label('Имя') ?>
        <?= $form->field($model->editModel, 'patronymic')->textInput()->label('Отчество') ?>

        <?= $form->field($model->editModel, 'birthdate')->widget(DatePicker::class, [
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
                'yearRange' => '1980:2100',
            ]])->label('Дата рождения') ?>
        <div>
            <?= $form->field($model->editModel, 'sex')->radioList(array(0 => 'Мужской',
                1 => 'Женский', 2 => 'Другое'), ['value' => $model->sex, 'class' => 'i-checks',
                    'item' => function($index, $label, $name, $checked, $value) {
                        if ($checked) {
                            $checkedStr = 'checked=""';
                        }
                        else {
                            $checkedStr = '';
                        }
                        $return = '<label class="modal-radio">';
                        $return .= '<input id="rl'.$index.'" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3" style="margin-right: 5px" '.$checkedStr.'>';
                        $return .= '<i></i>';
                        $return .= '<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';

                        return $return;
                    }
                ])->label('Пол') ?>
        </div>

        <?= $form->field($model->editModel, 'email')->textInput()->label('E-mail') ?>

        <?= $form->field($model->editModel, 'pd')->checkboxList(Yii::$app->personalData->getList(), ['item' => function ($index, $label, $name, $checked, $value) {
            if ($checked == 1) {
                $checked = 'checked';
            }
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="branch-'. $index .'">
                            <input class="eb1" id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
        }])->label('Запретить разглашение персональных данных:'); ?>

    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div class="form-group">
        <?= Html::submitButton('Объединить участников деятельности', ['id' => 'sub', 'class' => 'btn btn-primary', 'style' =>'display: none',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    function CheckFieldsFill()
    {
        let elem1 = document.getElementById('participant_id1');
        let elem2 = document.getElementById('participant_id2');

        if (elem1.value !== '' && elem1.value === elem2.value)
        {
            alert('Выбраны одинаковые участники! Обновите страницу и выберите разных участников');
            return;
        }

        if (elem1.value && elem2.value)
        {
            let main = document.getElementById('commonBlock');
            if (main !== null) {
                main.style.display = 'block';
            }

            main = document.getElementById('sub');
            if (main !== null) {
                main.removeAttribute('disabled');
            }

            main = document.getElementById('mergeparticipantmodel-fio1');
            console.log(main);
            if (main !== null) {
                main.setAttribute('readonly', 'true');
            }

            main = document.getElementById('mergeparticipantmodel-fio2');
            if (main !== null) {
                main.setAttribute('readonly', 'true');
            }
        }
    }

    function FillEditForm()
    {
        let main = document.getElementById('editBlock');
        main.style.display = 'block';
        main = document.getElementById('fill1');
        main.style.display = 'none';
        main = document.getElementById('sub');
        main.style.display = 'block';
        //заполняем поля редактируемой формы
        main = document.getElementById('foreigneventparticipantswork-secondname');
        let temp = document.getElementById('td-secondname-1');
        main.value = temp.innerHTML;

        main = document.getElementById('foreigneventparticipantswork-firstname');
        temp = document.getElementById('td-firstname-1');
        main.value = temp.innerHTML;

        main = document.getElementById('foreigneventparticipantswork-patronymic');
        temp = document.getElementById('td-patronymic-1');
        main.value = temp.innerHTML;

        main = document.getElementById('foreigneventparticipantswork-birthdate');
        temp = document.getElementById('td-birthdate-1');
        main.value = temp.innerHTML;

        let main1 = document.getElementById('rl0');
        let main2 = document.getElementById('rl1');
        let main3 = document.getElementById('rl2');
        temp = document.getElementById('td-sex-1');
        if (temp.innerHTML == 'Мужской') main1.setAttribute('checked', true);
        if (temp.innerHTML == 'Женский') main2.setAttribute('checked', true);
        if (temp.innerHTML == 'Другое') main3.setAttribute('checked', true);

        main = document.getElementsByClassName('b1');
        temp = document.getElementsByClassName('eb1');
        console.log(main);
        for (let i = 0; i < main.length; i++)
        {
            if (main[i].innerHTML == 'Запрещено') temp[i].setAttribute('checked', true);
        }
        
        main = document.getElementById('mergeparticipantmodel-id1');
        temp = document.getElementById('participant_id1');
        main.value = temp.value;

        main = document.getElementById('mergeparticipantmodel-id2');
        temp = document.getElementById('participant_id2');
        main.value = temp.value;

        //----------------------------------
    }

</script>