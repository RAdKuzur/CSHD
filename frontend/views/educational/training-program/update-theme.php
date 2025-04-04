<?php

use frontend\models\work\educational\training_program\ThematicPlanWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ThematicPlanWork */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Редактировать занятие из УТП';
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="substrate">
    <h3><?= Html::encode($this->title) ?></h3>
</div>

<div class="temporary-journal-form field-backing">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'theme')->textInput()->label('Тема') ?>

    <?= $form->field($model, 'control_type')->dropDownList(Yii::$app->controlType->getList(), ['prompt' => '---'])->label('Форма контроля'); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>