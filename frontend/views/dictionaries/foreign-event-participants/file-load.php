<?php

use frontend\models\work\auxiliary\LoadParticipants;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LoadParticipants */

$this->title = 'Добавление новых участников деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-participants-load-file field-backing">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'file')->fileInput()->label('Файл') ?>

    <div class="form-group">
        <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
