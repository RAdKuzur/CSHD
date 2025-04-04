<?php

use frontend\models\work\order\OrderMainWork;
use frontend\forms\ResponsibilityForm;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\responsibility\LocalResponsibilityWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ResponsibilityForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $audsList */
/* @var $peoples */
/* @var OrderMainWork $order текущий приказ, прикрепленный к ответственности */
/* @var array $orders список всех доступных приказов */
/* @var array $regulations список всех доступных положений */
/* @var $files */
/* @var $modelResponsibility LocalResponsibilityWork */
?>

<script>
    let is_data_changed = true;
    window.onbeforeunload = function () {
        return (is_data_changed ? "Измененные данные не сохранены. Закрыть страницу?" : null);
    }

    function clickSubmit()
    {
        is_data_changed = false;
    }

</script>

<div class="local-responsibility-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php

    $params = [
        'disabled' => $model->branch !== null ? 'disabled' : null,
    ];
    echo $form->field($model, 'responsibilityType')->dropDownList(Yii::$app->responsibilityType->getList(), $params);

    ?>

    <?php
    $params = [
        'disabled'=> $model->branch !== null ? 'disabled' : null,
        'prompt' => '--',
        'onchange' => '
            $.post(
                "' . Url::toRoute('get-auditorium') . '", 
                {id: $(this).val()}, 
                function(res){
                    var elem = document.getElementsByClassName("aud");
                    elem[0].innerHTML = res;
                }
            );
        ',
    ];
    echo $form->field($model, 'branch')->dropDownList(Yii::$app->branches->getList(), $params);

    ?>

    <?php

    $params = [
        'disabled'=> $model->branch !== null ? 'disabled' : null,
        'class' => 'form-control aud',
        'prompt' => '---',
    ];

    $items = [];
    if ($model->branch !== null) {
        $items = $audsList;
    }

    echo $form->field($model, 'auditoriumId')->dropDownList(ArrayHelper::map($audsList,'id','name'), $params);

    ?>

    <?= $form->field($model, 'quant')->input('text', ['placeholder' => "Введите целое число, если необходима дополнительная идентификация ответственности", 'readonly' => 'true']); ?>

    <?php

    if (!$model->isAttach()) {
        echo $form->field($model, 'peopleStampId')->dropDownList(ArrayHelper::map($peoples,'id','fullFio'), ['prompt' => '---']);
        echo $form->field($model, 'startDate')->widget(DatePicker::class, [
            'language' => 'ru',
            'dateFormat' => 'dd.MM.yyyy',
            'options' => [
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '1950:2100',
            ]])->label('Дата прикрепления ответственности');
    }
    else {
        // возможно стоит вынести оформление таблицы в к-нибудь билдер
        echo '<table class="table table-bordered">'.
             '<tr><td><b>Ответственное лицо</b></td><td><b>Дата открепления ответственности</b></td><td><b>Приказ</b></td></tr>';
        echo '<tr><td>'.$modelResponsibility->peopleStampWork->peopleWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS).'</td><td>';
        echo $form->field($model, 'endDate')->widget(DatePicker::class, [
            'language' => 'ru',
            'dateFormat' => 'dd.MM.yyyy',
            'options' => [
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '1950:2100',
            ]])->label(false);
        echo '</td><td>'.Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id])). '</td>';
        echo '</td><td>'.Html::submitButton('Открепить', ['class' => 'btn btn-danger', 'onclick' => 'clickSubmit()']). '</td><tr>';
        echo '</table>';
    }

    ?>

    <?= $form->field($model, 'orderId')->dropDownList(ArrayHelper::map($orders,'id','fullName'), ['prompt' => '---'])->label('Приказ'); ?>

    <?= $form->field($model, 'regulationId')->dropDownList(ArrayHelper::map($regulations,'id','name'), ['prompt' => '---']); ?>

    <?= $form->field($model, 'filesList[]')->fileInput(['multiple' => true]) ?>

    <?php if (strlen($files) > 10): ?>
        <?= $files; ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success', 'onclick' => 'clickSubmit()']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
