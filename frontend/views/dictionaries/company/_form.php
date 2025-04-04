<?php

use frontend\models\work\dictionaries\CompanyWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model CompanyWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inn')->textInput()->label('ИНН организации'); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Название организации') ?>
    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true])->label('Краткое название организации') ?>
    <?= $form->field($model, 'company_type')->dropDownList(Yii::$app->companyType->getList())->label('Тип организации'); ?>
    <?= $form->field($model, 'is_contractor')->checkbox(['onchange' => 'ContractorChange(this)']); ?>


    <div id="contractor" style="display: <?= $model->is_contractor == 1 ? 'block' : 'none' ?>">
        <?= $form->field($model, 'category_smsp')->dropDownList(Yii::$app->categorySmsp->getList(), ['prompt' => 'НЕ СМСП'])->label('Категория СМСП'); ?>
        <?= $form->field($model, 'ownership_type')->dropDownList(Yii::$app->ownershipType->getList(), ['prompt' => '---'])->label('Форма собственности'); ?>
        <?= $form->field($model, 'okved')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'head_fio')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'site')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'comment')->textarea(['rows' => '3']) ?>   
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
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