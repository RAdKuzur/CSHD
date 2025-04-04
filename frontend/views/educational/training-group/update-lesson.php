<?php

use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrainingGroupLessonWork */
/* @var $auds AuditoriumWork[] */
/* @var $form ActiveForm */

$this->title = 'Редактирование занятия';
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Группа {$model->trainingGroupWork->number}", 'url' => ['view', 'id' => $model->training_group_id]];
$this->params['breadcrumbs'][] = ['label' => "Редактирование расписания", 'url' => ['schedule-form', 'id' => $model->training_group_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="foreign-event-participants-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lesson_date')->widget(DatePicker::class,
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата проведения занятия',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]])->label('Дата занятия') ?>

    <div class="col-xs-2" style="padding-left: 0;">
        <?= $form->field($model, 'lesson_start_time')->textInput(
                [
                    'type' => 'time',
                    'class' => 'form-control def',
                    'min' => '08:00',
                    'max' => '20:00'
                ]
            )->label('Начало занятия') ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <?php

    $params = [
        'onchange' => '
            $.post(
                "' . Url::toRoute('sub-auds') . '", 
                {branch: $(this).val()}, 
                function(res){
                    var elem = document.getElementsByClassName("aud");
                    elem[0].innerHTML = res;
                }
            );
        ',
    ];

    echo $form->field($model, 'branch')->dropDownList(
            Yii::$app->branches->getOnlyEducational(), $params
    )->label('Отдел');

    $params = [
        'prompt' => 'Вне отдела',
        'class' => 'form-control aud',
    ];

    $items = [];
    if (!is_null($model->branch)) {
        $items = ArrayHelper::map($auds,'id','fullName');
    }

    echo $form->field($model, 'auditorium_id')->dropDownList($items, $params)->label('Помещение');
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
