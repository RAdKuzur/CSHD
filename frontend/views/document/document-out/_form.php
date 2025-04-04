<?php

use common\helpers\DateFormatter;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\document_in_out\DocumentOutWork */
/* @var $correspondentList */
/* @var $availablePositions */
/* @var $availableCompanies */
/* @var $mainCompanyWorkers */
/* @var $scanFile */
/* @var $docFiles */
/* @var $appFiles */
/* @var $filesAnswer */


/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-out-form field-backing">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'document_date')->widget(DatePicker::class, [
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
            'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
        ]])->label('Дата документа') ?>
    <?= $form->field($model, 'document_theme')->textInput(['maxlength' => true])->label('Тема документа') ?>

    <?php

    $params = [
        'prompt' => 'Выберите корреспондента',
        'onchange' => '
        $.post(
            "' . Url::toRoute('dependency-dropdown') . '", 
            {id: $(this).val()}, 
            function(res){
                var resArr = res.split("|split|");
                var elem = document.getElementsByClassName("pos");
                elem[0].innerHTML = resArr[0];
                elem = document.getElementsByClassName("com");
                elem[0].innerHTML = resArr[1];
                $("#company").trigger("change");
                $("#position").trigger("change");
            }
        );
    ',
    ];

    echo $form->field($model, 'correspondent_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map($mainCompanyWorkers,'id','fullFio'),
        'size' => Select2::LARGE,
        'options' => $params,
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label('ФИО корреспондента');
    ?>

    <div id="corr_div2">
        <?php
        $params = [
            'id' => 'company',
            'class' => 'form-control com',
            'prompt' => '---',
        ];

        echo $form->field($model, 'company_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map($availableCompanies, 'id', 'name'),
            'size' => Select2::LARGE,
            'options' => $params,
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('Организация корреспондента');
        ?>
    </div>
    <div id="corr_div1">
        <?php
        $params = [
            'id' => 'position',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];

        echo $form->field($model, 'position_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map($availablePositions, 'id', 'name'),
            'size' => Select2::LARGE,
            'options' => $params,
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('Должность корреспондента (при наличии)');
        ?>
    </div>

    <div id="creator_div1">
        <?php
        $params = [
            'id' => 'creator',
            'class' => 'form-control cre',
            'prompt' => '---',
        ];

        echo $form->field($model, 'signed_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map($correspondentList, 'id','fullFio'),
            'size' => Select2::LARGE,
            'options' => $params,
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('Кем подписан');
        ?>
    </div>

    <div id="creator_div2">
        <?php
        $params = [
            'id' => 'creator1',
            'class' => 'form-control cre',
            'prompt' => '---',
        ];

        echo $form->field($model, 'executor_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map($correspondentList, 'id','fullFio'),
            'size' => Select2::LARGE,
            'options' => $params,
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('Кто исполнил');
        ?>
    </div>
    <?= $form->field($model, 'send_method')->dropDownList(Yii::$app->sendMethods->getList())->label('Способ отправки') ?>
    <?= $form->field($model, 'sent_date')->widget(DatePicker::class, [
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
            'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
        ]])->label('Дата отправки') ?>
    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true])->label('Ключевые слова') ?>
    <?= $form->field($model, 'isAnswer')->checkbox(['id' => 'isAnswer', 'onchange' => 'checkAnswer()']) ?>
    <!-- <div id="dateAnswer" class="col-xs-4" <?= $model->isAnswer == 0 ? 'hidden' : '' ?>>
        <?= $form->field($model, 'dateAnswer')->widget(DatePicker::class, [
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
                'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
            ]])->label('Крайний срок ответа')?>
    </div>
    -->
    <div id="filesAnswer" class="col-xs-4" <?= $model->isAnswer == 0 ? 'hidden' : '' ?>>
        <?php
        $params = [
            'prompt' => ''
        ];
       echo $form->field($model, "is_answer")
           ->dropDownList(
                ArrayHelper::map($filesAnswer,'id',function($model){

                    return  $model->local_number.' '.$model->document_theme;
                }),
                $params
            )
            ->label('Является ответом на');
        ?>
    </div>


    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <?= $form->field($model, 'scanFile')->fileInput()
        ->label('Скан документа') ?>

    <?php if (strlen($scanFile) > 10): ?>
        <?= $scanFile; ?>
    <?php endif; ?>

    <?= $form->field($model, 'docFile[]')
        ->fileInput(['multiple' => true])
        ->label('Редактируемые документы') ?>

    <?php if (strlen($docFiles) > 10): ?>
        <?= $docFiles; ?>
    <?php endif; ?>

    <?= $form->field($model, 'appFile[]')
        ->fileInput(['multiple' => true])
        ->label('Приложения') ?>

    <?php if (strlen($appFiles) > 10): ?>
        <?= $appFiles; ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
    function checkAnswer()
    {
        var chkBox = document.getElementById('isAnswer');
        if (chkBox.checked)
        {
            $("#dateAnswer").removeAttr("hidden");
            $("#filesAnswer").removeAttr("hidden");
        }
        else
        {
            $("#dateAnswer").attr("hidden", "true");
            $("#filesAnswer").attr("hidden", "true");
        }
    }
</script>
<script>
    $("#corr").change(function() {
        if (this.value != '') {
            $("#corr_div1").attr("hidden", "true");
            $("#corr_div2").attr("hidden", "true");
        }
        else
        {
            $("#corr_div1").removeAttr("hidden");
            $("#corr_div2").removeAttr("hidden");
        }
    });
</script>