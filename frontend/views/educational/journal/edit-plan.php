<?php

use common\helpers\DateFormatter;
use common\helpers\html\HtmlBuilder;
use frontend\forms\journal\ThematicPlanForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ThematicPlanForm */

$this->title = 'Редактирование тематического плана';
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_INDEX]];
$this->params['breadcrumbs'][] = ['label' => 'Группа ' . $model->getTrainingGroupNumber(), 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $model->groupId]];
$this->params['breadcrumbs'][] = ['label' => 'Электронный журнал', 'url' => [Yii::$app->frontUrls::JOURNAL_VIEW, 'id' => $model->groupId]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="plan-edit">

    <?php $form = ActiveForm::begin(); ?>
    <div class="substrate">
        <div class="flexx">
            <h1>
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
        <div class="flexx space">
            <div class="flexx">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <div class="plan-form">
        <?= $form->field($model, 'groupId')->hiddenInput()->label(false) ?>
        <div class="card no-flex">
            <div class="table-topic">
                Тематический план (ТП)
            </div>
            <div class="table-block">
                <table>
                    <thead>
                        <tr>
                            <th>Дата занятия</th>
                            <th>Тема занятия</th>
                            <th>Форма контроля</th>
                            <th>ФИО педагога</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($model->lessonThemes as $index => $lessonTheme): ?>
                        <?= $form->field($lessonTheme, "id[$index]")->hiddenInput(['value' => $lessonTheme->id])->label(false) ?>
                        <tr>
                            <td>
                                <?=
                                    DateFormatter::format(
                                        $lessonTheme->trainingGroupLessonWork->lesson_date,
                                        DateFormatter::Ymd_dash,
                                        DateFormatter::dmY_dot
                                    )
                                ?>
                            </td>
                            <td>
                                <?= $lessonTheme->thematicPlanWork->theme ?>
                            </td>
                            <td>
                                <?= Yii::$app->controlType->get($lessonTheme->thematicPlanWork->control_type) ?>
                            </td>
                            <td>
                                <?= $form->field($lessonTheme, "teacher_id[$index]")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map($model->availableTeachers,'id','fullFio'),
                                    'size' => Select2::LARGE,
                                    'options' => [
                                        'value' => $lessonTheme->teacherWork->people_id
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ])->label(false);

                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <?= HtmlBuilder::upButton();?>
</div>

