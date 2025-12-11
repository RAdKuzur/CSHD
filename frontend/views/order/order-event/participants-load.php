<?php

use frontend\models\work\auxiliary\OrderEventParticipantLoader;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Загрузка участников в приказ';
$this->params['breadcrumbs'][] = ['label' => 'Приказы по учету достижений', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Приказ $orderName", 'url' => ['view', 'id' => $orderId]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-event-participants-load field-backing">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'action' => ['process-participants-excel', 'id' => $orderId],
    ]); ?>

    <?= $form->field($model, 'file')->fileInput()->label('Excel-файл') ?>

    <div class="form-group"> 
        <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отмена', ['view', 'id' => $orderId], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>