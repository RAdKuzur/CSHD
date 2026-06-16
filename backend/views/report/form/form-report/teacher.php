<?php

?>
<?php use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var \backend\forms\report\TeacherReportForm $model **/
$form = ActiveForm::begin();
$years = array_combine(
    range(date('Y') - 10, date('Y')),
    range(date('Y') - 10, date('Y'))
);
?>
    <div id="year-select">
        <?=
        $form
            ->field($model, 'year')
            ->dropDownList($years, ['prompt' => '---'])
            ->label('Год');
        ?>
    </div>
    <div id="branch-select">
        <?=
        $form
            ->field($model, 'branch')
            ->dropDownList(Yii::$app->branches->getList(), ['prompt' => '---'])
            ->label('Отдел');
        ?>
    </div>
    <div>
        <?= $form->field($model, 'budget')->checkboxList([
            0 => 'Внебюджетная',
            1 => 'Бюджетная'
        ])->label('Основа'); ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Скачать отчет', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>