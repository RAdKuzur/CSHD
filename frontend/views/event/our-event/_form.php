<?php

use common\helpers\DateFormatter;
use frontend\models\work\event\EventWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model EventWork */
/* @var $people PeopleWork */
/* @var $regulations RegulationWork */
/* @var $branches array */
/* @var $protocolFiles */
/* @var $photoFiles */
/* @var $reportingFiles */
/* @var $otherFiles */
/* @var $form yii\widgets\ActiveForm */
?>

<script type="text/javascript">
    window.onload = function(){
        let elem = document.getElementById('all_scopes');
        let ids = elem.innerHTML.split(' ');

        let checks = document.getElementsByClassName('sc');

        for (let i = 0; i < ids.length; i++)
            for (let j = 0; j < checks.length; j++)
                if (ids[i] == checks[j].value)
                    checks[j].setAttribute('checked', 'checked');
    }
</script>

<script src="/scripts/sisyphus/sisyphus.js"></script>
<script src="/scripts/sisyphus/sisyphus.min.js"></script>


<div class="event-form field-backing">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'start_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата начала мероприятия',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
        ]])->label('Дата начала мероприятия') ?>

    <?= $form->field($model, 'finish_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата окончания мероприятия',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
        ]])->label('Дата окончания мероприятия') ?>

    <?= $form->field($model, 'event_type')->dropDownList(Yii::$app->eventType->getList(), [])->label('Тип мероприятия'); ?>
    <?= $form->field($model, 'event_form')->dropDownList(Yii::$app->eventForm->getList(), [])->label('Форма мероприятия'); ?>
    <?= $form->field($model, 'event_way')->dropDownList(Yii::$app->eventWay->getList(), [])->label('Формат проведения'); ?>
    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'event_level')->dropDownList(Yii::$app->eventLevel->getList(), [])->label('Уровень мероприятия'); ?>

    <div class="checkList">
        <div class="checkHeader">
            <h4 class="noPM">Сферы участия</h4>
        </div>

        <div class="checkBlock">
            <?= $form->field($model, 'scopes')->checkboxList(Yii::$app->participationScope->getList(), [
                'item' => function($index, $label, $name, $checked, $value) {
                $checked = $checked ? 'checked' : '';
                return "<div 'class'='col-sm-12'><label><input class='sc' type='checkbox' {$checked} name='{$name}'value='{$value}'> {$label}</label></div>";
            }])->label(false) ?>
        </div>

    </div>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Участники</h4></div>
            <div class="panel-body">
                <?= $form->field($model, 'child_participants_count')->textInput(['value' => $model->child_participants_count == null ? 0 : $model->child_participants_count]) ?>
                <?= $form->field($model, 'child_rst_participants_count')->textInput(['value' => $model->child_rst_participants_count == null ? 0 : $model->child_rst_participants_count]) ?>
                <?= $form->field($model, 'age_left_border')->textInput(['value' => $model->age_left_border == null ? 5 : $model->age_left_border]) ?>
                <?= $form->field($model, 'age_right_border')->textInput(['value' => $model->age_right_border == null ? 18 : $model->age_right_border]) ?>

                <?= $form->field($model, 'teacher_participants_count')->textInput(['value' => $model->teacher_participants_count == null ? 0 : $model->teacher_participants_count]) ?>
                <?= $form->field($model, 'other_participants_count')->textInput(['value' => $model->other_participants_count == null ? 0 : $model->other_participants_count]) ?>
            </div>
        </div>
    </div>

    <?php //echo $form->field($model, 'is_federal')->checkbox() ?>

    <?= $form->field($model, 'responsible1_id')->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), [])->label('Ответственный за мероприятие'); ?>
    <?= $form->field($model, 'responsible2_id')->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), ['prompt' => '---'])->label('Второй ответственный (опционально)'); ?>

    <div class="checkList">
        <div class="checkHeader">
            <h4 class="noPM">Отделы</h4>
        </div>

        <div class="checkBlock">
            <?= $form->field($model, 'branches')->checkboxList(Yii::$app->branches->getOnlyEducational(), [
                'item' => function($index, $label, $name, $checked, $value) {
                    $checked = $checked ? 'checked' : '';
                    return "<div 'class'='col-sm-12'><label><input class='sc' type='checkbox' {$checked} name='{$name}'value='{$value}'> {$label}</label></div>";
                }])->label(false) ?>
        </div>
    </div>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'regulation_id')->dropDownList(ArrayHelper::map($regulations, 'id', 'name'), ['prompt' => 'Нет'])->label('Положение по мероприятию'); ?>

    <div class="checkList">
        <div class="checkHeader">
            <h4 class="noPM">Образовательные программы</h4>
        </div>

        <div class="checkBlock">
            <?= $form->field($model, 'contains_education')->radioList(array(0 => 'Не содержит',
                1 => 'Содержит'), ['value'=>$model->contains_education ])->label('') ?>
        </div>
    </div>


    <?= $form->field($model, 'protocolFiles[]')->fileInput(['multiple' => true]) ?>

    <?php if (strlen($protocolFiles) > 10): ?>
        <?= $protocolFiles; ?>
    <?php endif; ?>

    <?= $form->field($model, 'photoFiles[]')->fileInput(['multiple' => true]) ?>

    <?php if (strlen($photoFiles) > 10): ?>
        <?= $photoFiles; ?>
    <?php endif; ?>

    <?= $form->field($model, 'reportingFiles[]')->fileInput(['multiple' => true]) ?>

    <?php if (strlen($reportingFiles) > 10): ?>
        <?= $reportingFiles; ?>
    <?php endif; ?>

    <?= $form->field($model, 'otherFiles[]')->fileInput(['multiple' => true]) ?>

    <?php if (strlen($otherFiles) > 10): ?>
        <?= $otherFiles; ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    $('form').sisyphus();
    
    var reloaded  = function(){alert('reload');} //страницу перезагрузили
    window.onload = function() {
      var loaded = sessionStorage.getItem('loaded');
      if(loaded) {
        reloaded();
      } else {
        sessionStorage.setItem('loaded', true);
      }
    }
</script>