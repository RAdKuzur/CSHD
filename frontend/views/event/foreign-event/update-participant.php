<?php

use common\components\dictionaries\base\BranchDictionary;
use frontend\forms\event\EventParticipantForm;
use frontend\models\work\general\PeopleWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model EventParticipantForm */


$this->title = $model->actParticipant->getSquadName();

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-participant-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'teacher')->textInput(['readonly' => true, 'value' => $model->actParticipant->teacherWork->getFIO(PeopleWork::FIO_FULL)])->label('ФИО педагогов'); ?>
    <?= $form->field($model, 'teacher2')->textInput(['readonly' => true, 'value' => $model->actParticipant->teacher2Work->getFIO(PeopleWork::FIO_FULL)])->label(false); ?>
    <?= $form->field($model, 'focus')->textInput(['readonly' => true, 'value' => Yii::$app->focus->get($model->actParticipant->focus)])->label('Направленность'); ?>
    <?= $form->field($model, 'nomination')->textInput(['readonly' => true, 'value' => $model->actParticipant->nomination])->label('Номинация'); ?>

    <fieldset disabled>
        <?=
            $form->field($model, 'branches')->checkboxList(
                Yii::$app->branches->getList(), ['class' => 'base',
                    'item' => function ($index, $label, $name, $checked, $value) {
                        if ($checked == 1) {
                            $checked = 'checked';
                        }
                        return
                            '<div class="checkbox" class="form-control">
                            <label style="margin-bottom: 0px" for="branch-' . $index .'">
                                <input id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                                '. $label .'
                            </label>
                        </div>';
                    }]
            )->label('<u>Отдел(-ы)</u>')
        ?>
    </fieldset>

    <?= $form->field($model, 'form')->dropDownList(
            Yii::$app->allowRemote->getList(),
            array_merge(
                ['id' => 'allow_id', 'value' => $model->actParticipant->form],
                !in_array(BranchDictionary::COD, $model->branches)
                    ? ['disabled' => true]
                    : []
            )
        )->label('Форма реализации');
    ?>


    <?= $form->field($model, 'fileMaterial')->fileInput()->label('Представленные материалы') ?>
    <?php if (strlen($model->fileMaterialTable) > 10): ?>
        <?= $model->fileMaterialTable; ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>