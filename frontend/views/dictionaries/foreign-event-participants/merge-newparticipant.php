<?php

use frontend\forms\participants\MergeNewParticipantForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \frontend\models\work\dictionaries\PersonInterface;
/* @var $this yii\web\View */
/* @var $model MergeNewParticipantForm */

$this->title = 'Подтверждение смены email';
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin(); ?>

    <p>Найдены участники с одинаковыми ФИО и датами рождения, но разными email. Подтвердите смену почты:</p>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ФИО</th>
            <th>Дата рождения</th>
            <th>Текущий email</th>
            <th>Новый email</th>
            <th>
                <input type="checkbox" id="select-all" title="Выбрать все">
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($model->originals as $index => $original): ?>
            <?php $duplicate = $model->duplicates[$index] ?? null; ?>
            <?php if ($duplicate): ?>
                <tr>
                    <td><?= Html::encode($original->getFIO(1)) ?></td>
                    <td><?= Yii::$app->formatter->asDate($original->birthdate, 'dd.MM.yyyy') ?></td>
                    <td><?= Html::encode($original->email) ?></td>
                    <td><?= Html::encode($duplicate->email) ?></td>
                    <td>
                        <?= $form->field($model, "selected[{$index}]")->checkbox([
                            'value' => $original->id,
                            'label' => false,
                            'uncheck' => null,
                            'class' => 'participant-checkbox'
                        ]) ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="form-group">
        <?= Html::submitButton('Применить выбранные изменения', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Пропустить смену почты', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
// Выделить все checkbox
$(document).on('click', '#select-all', function() {
    $('.participant-checkbox').prop('checked', $(this).prop('checked'));
});

// Если вручную отмечают все checkbox, отметить и "Выбрать все"
$(document).on('change', '.participant-checkbox', function() {
    var allChecked = $('.participant-checkbox:checked').length === $('.participant-checkbox').length;
    $('#select-all').prop('checked', allChecked);
});

// Проверка что хотя бы один checkbox выбран
$('form').on('submit', function(e) {
    if ($('.participant-checkbox:checked').length === 0) {
        alert('Выберите хотя бы одного участника для изменения');
        e.preventDefault();
    }
});
JS;
$this->registerJs($js);
?>