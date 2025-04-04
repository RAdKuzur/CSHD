<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model \frontend\models\work\document_in_out\DocumentOutWork */
/* @var $peopleList */

?>


<?php
    Modal::begin([
        'id' => 'modal-reserve',
        'title' => 'Добавить резерв',
    ]);
    $form = ActiveForm::begin();
    $params = [
        'prompt' => '---',
        'onchange' => '
            $.post(
                "' . Url::toRoute('dependency-dropdown') . '", 
                {id: $(this).val()}, 
                function(res){
                    var resArr = res.split("|split|");
                    var elem = document.getElementsByClassName("pos");
                    elem[0].innerHTML = resArr[0];
                    elem = document.getElementsByClassName("com");
                    elem[0].innerHTML = resArr[1];
                }
            );
        ',
    ];
    echo $form
        ->field($model, 'executor_id')
        ->dropDownList(ArrayHelper::map($peopleList, 'id','fullFio'), $params)
        ->label('Кто исполнил');

    echo $form->field($model, 'document_date')->widget(DatePicker::class, [
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
            'yearRange' => '2000:2100',
        ]])->label('Дата документа')
?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end();
    Modal::end();
?>