<?php

use frontend\forms\event\ParticipantAchievementForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ParticipantAchievementForm */


$this->title = $model->entity->actParticipantWork->getSquadName();

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-participant-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'achievement')->textInput()->label('Достижение'); ?>
    <?= $form->field($model, 'certNumber')->textInput()->label('Номер наградного документа'); ?>
    <?= $form->field($model, 'date')->textInput([
        'type' => 'date',
        'class' => 'form-control'
    ])->label('Дата наградного документа'); ?>

    <div class="toggle-wrapper form-group field-participantachievementwork-type">
        <input type="hidden" name="ParticipantAchievementWork[type]" value="0">
        <input
            type="checkbox"
            value="1"
            id="participantachievementwork-type"
            class="toggle-checkbox"
            name="ParticipantAchievementWork[type]" <?= $model->type == 1 ? "checked" : "" ?>>
        <span class="toggle-icon off">Призер</span>
        <div class="toggle-container">
            <div class="toggle-button"></div>
        </div>
        <span class="toggle-icon on">Победитель</span>
        <div class="help-block"></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style type="text/css">
    .toggle-wrapper {
        margin-top: 2.5em;
        position: relative;
        display: flex;
        align-items: center;
        column-gap: .25em;
        margin-bottom: 2.5em;
    }

    .toggle-checkbox:not(:checked) + .off,
    .toggle-checkbox:checked ~ .on {
        font-weight: 700;
    }

    .toggle-checkbox {
        -webkit-appearance: none;
        appearance: none;
        position: absolute;
        z-index: 1;
        border-radius: 3.125em;
        width: 4.05em;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        margin-left: 4em!important;
    }

    .toggle-container {
        position: relative;
        border-radius: 3.125em;
        width: 4.05em;
        height: 1.5em;
        background-color: #ccc;
        background-size: .125em .125em;
    }

    .toggle-button {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: .0625em;
        left: .0625em;
        border-radius: inherit;
        width: 2.55em;
        height: calc(100% - .125em);
        background-color: #FFA23A;
        box-shadow: 0 .125em .25em rgb(0 0 0 / .6);
        transition: left .4s;

    .toggle-checkbox:checked ~ .toggle-container > & {
        left: 1.4375em;
    }

    &::before {
         content: '';
         position: absolute;
         top: inherit;
         border-radius: inherit;
         width: calc(100% - .375em);
         height: inherit;
     }

    &::after {
         content: '';
         position: absolute;
         width: .5em;
         height: 38%;
     }
    }
</style>