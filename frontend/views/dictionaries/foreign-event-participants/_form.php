<?php

use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ForeignEventParticipantsWork */
/* @var $form yii\bootstrap5\ActiveForm */
?>

<script src="/scripts/sisyphus/sisyphus.js"></script>
<script src="/scripts/sisyphus/sisyphus.min.js"></script>

<div class="foreign-event-participants-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'surname')->textInput() ?>

    <?= $form->field($model, 'firstname')->textInput() ?>

    <?= $form->field($model, 'patronymic')->textInput() ?>

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
        ]]) ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <div>
        <?= $form->field($model, 'sex')->radioList(array(
                0 => 'Мужской',
                1 => 'Женский',
                2 => 'Другое'
        ), ['value' => $model->sex, 'class' => 'i-checks']) ?>
    </div>

    <!--if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 6) || \app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7))-->

    <div <?= $model->isTrueAnyway() ? 'hidden' : '' ?>>
        <?= $form->field($model, 'guaranteed_true')->checkbox(['checked' => $model->isGuaranteedTrue()]); ?>
    </div>

    <!--if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 22) )-->

    <?= $form->field($model, 'pd')->checkboxList(Yii::$app->personalData->getList(), ['item' => function ($index, $label, $name, $checked, $value) {
            if ($checked == 1) $checked = 'checked';
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="branch-'. $index .'">
                            <input id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
        }])->label('Запретить разглашение персональных данных:');
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    $('form').sisyphus();
</script>