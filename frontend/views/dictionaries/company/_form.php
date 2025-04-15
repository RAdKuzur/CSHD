<?php

use frontend\models\work\dictionaries\CompanyWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model CompanyWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form field-backing">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inn')->textInput()->label('ИНН организации'); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Название организации') ?>
    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true])->label('Краткое название организации') ?>
    <?= $form->field($model, 'company_type')->dropDownList(Yii::$app->companyType->getList())->label('Тип организации'); ?>
    <?= $form->field($model, 'is_contractor')->checkbox(['onchange' => 'ContractorChange(this)']); ?>


    <div id="contractor" style="display: <?= $model->is_contractor == 1 ? 'block' : 'none' ?>">
        <?= $form->field($model, 'category_smsp')->dropDownList(Yii::$app->categorySmsp->getList(), ['prompt' => 'НЕ СМСП'])->label('Категория СМСП'); ?>
        <?= $form->field($model, 'ownership_type')->dropDownList(Yii::$app->ownershipType->getList(), ['prompt' => '---'])->label('Форма собственности'); ?>
        <?= $form->field($model, 'okved')->textInput(['maxlength' => true])->label('ОКВЭД') ?>
        <?= $form->field($model, 'head_fio')->textInput(['maxlength' => true])->label('ФИО Директора') ?>
        <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true])->label('Номер телефона') ?>
        <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label('Адрес электронной почты') ?>
        <?= $form->field($model, 'site')->textInput(['maxlength' => true])->label('Адрес веб-сайта') ?>
        <?= $form->field($model, 'comment')->textarea(['rows' => '3'])->label('Комментарий') ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    function ContractorChange(e)
    {
        let elem = document.getElementById('contractor');
        if (e.checked)
            elem.style.display = "block";
        else
            elem.style.display = "none";

    }
</script>