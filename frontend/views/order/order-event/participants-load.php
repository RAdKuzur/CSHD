<?php

use frontend\models\work\auxiliary\OrderEventParticipantLoader;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model OrderEventParticipantLoader */
/* @var $orderId int */
/* @var $orderName string */

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

    <input type="hidden" name="nomination" id="nomination-field"> 

    <?= $form->field($model, 'file')->fileInput()->label('Excel-файл') ?>

    <div class="form-group"> 
        <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отмена', ['update', 'id' => $orderId], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var nominationsJson = sessionStorage.getItem('nominations');
        
        if (nominationsJson) {
            var nominations = JSON.parse(nominationsJson);
            
            if (nominations.length > 0) {
                
                var lastNomination = nominations[nominations.length - 1];

                document.getElementById('nomination-field').value = lastNomination;
                
                console.log('Последняя номинация для БД:', lastNomination);

                var infoDiv = document.createElement('div');
                infoDiv.className = 'alert alert-info';
                infoDiv.innerHTML = '<strong>Используется номинация:</strong> ' + lastNomination;
                document.querySelector('.form-group').before(infoDiv);
            }
            
            sessionStorage.removeItem('nominations');
            sessionStorage.removeItem('orderId');
        }
    });
</script>
