<?php

use frontend\forms\event\ForeignEventForm;
use frontend\models\work\event\ParticipantAchievementWork;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model ForeignEventForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $peoples */
/* @var $orders6 */
/* @var $orders9 */
/* @var $modelAchievements */
?>

<style type="text/css">
    /*.button {
        position: fixed;
        bottom: 0px;
        background-color: #f5f8f9;
        width: 77%;
        padding-left: 1%;
        padding-top: 1%;
        padding-right: 1%;
        padding-bottom: 1%;
    }
    .test{
        height:1000px;

    }
    .row {
        margin: 0px;
    }

    .toggle-wrapper {

        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        column-gap: .25em;
    }

    .toggle-checkbox:not(:checked) + .off,
    .toggle-checkbox:checked ~ .on {
        font-weight: 700;
    }

    .toggle-checkbox {
        -webkit-appearance: none;
        appearance: none;
        position: absolute;
        z-index: 1;
        border-radius: 3.125em;
        width: 4.05em;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        margin-left: -2em!important;
    }

    .toggle-container {
        position: relative;
        border-radius: 3.125em;
        width: 4.05em;
        height: 1.5em;
        background-color: #ccc;
        background-size: .125em .125em;
    }

    .toggle-button {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: .0625em;
        left: .0625em;
        border-radius: inherit;
        width: 2.55em;
        height: calc(100% - .125em);
        background-color: #FFA23A;
        box-shadow: 0 .125em .25em rgb(0 0 0 / .6);
        transition: left .4s;

    .toggle-checkbox:checked ~ .toggle-container > & {
        left: 1.4375em;
    }

    &::before {
         content: '';
         position: absolute;
         top: inherit;
         border-radius: inherit;
         width: calc(100% - .375em);
         height: inherit;

     }

    &::after {
         content: '';
         position: absolute;
         width: .5em;
         height: 38%;

     }
    }*/
</style>


<script type="text/javascript">
    window.onload = function(){
        let elem = document.getElementsByClassName("date_achieve");
        let orig = document.getElementById('foreigneventwork-finish_date');
        elem[elem.length - 1].value = orig.value;
    }
</script>


<div class="foreign-event-form field-backing">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="col-xs-4">
        <label class="control-label">Название мероприятия</label>
        <select class="form-control" disabled>
            <option selected><?= $model->name ?></option>
        </select>
    </div>

    <div class="col-xs-4">
        <label class="control-label">Организатор</label>
        <select class="form-control" disabled>
            <option selected><?= $model->organizer ?></option>
        </select>
    </div>

    <div class="col-xs-4">
        <label class="control-label">Дата начала</label>
        <select class="form-control" disabled>
            <option selected><?= $model->startDate ?></option>
        </select>
    </div>

    <div class="col-xs-4">
        <label class="control-label">Дата окончания</label>
        <select class="form-control" disabled>
            <option selected><?= $model->endDate ?></option>
        </select>
    </div>

    <div class="col-xs-4">
        <label class="control-label">Город</label>
        <select class="form-control" disabled>
            <option selected><?= $model->city ?></option>
        </select>
    </div>

    <div class="col-xs-4">
        <label class="control-label">Формат проведения</label>
        <select class="form-control" disabled>
            <option selected><?= Yii::$app->eventWay->get($model->format) ?></option>
        </select>
    </div>

    <div class="col-xs-4">
        <label class="control-label">Уровень</label>
        <select class="form-control" disabled>
            <option selected><?= Yii::$app->eventLevel->get($model->level) ?></option>
        </select>
    </div>

    <?php
    $icon = '❌';
    if ($model->minister)
        $icon = '✅';
    echo '<div class="form-group field-foreigneventwork-is_minpros has-success"><label>'.$icon.' Входит в перечень Минпросвещения РФ</label><div class="help-block"></div></div>';
    ?>

    <div class="col-xs-4">
        <label class="control-label">Мин. возраст участников (лет)</label>
        <select class="form-control" disabled>
            <option selected><?= $model->minAge ?></option>
        </select>
    </div>

    <div class="col-xs-4">
        <label class="control-label">Макс. возраст участников (лет)</label>
        <select class="form-control" disabled>
            <option selected><?= $model->maxAge ?></option>
        </select>
    </div>

    <?php if (strlen($model->oldAchievements) > 10): ?>
        <?= $model->oldAchievements; ?>
    <?php endif; ?>

    <div class="row">
        <div class="panel panel-default">
            <?php if (strlen($model->squadParticipants) > 10): ?>
                <div class="panel-heading"><h4><i class="glyphicon glyphicon-user"></i>Акты участия</h4></div>
                <?= $model->squadParticipants; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="bordered-div">
        <?php
            DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper',
                'widgetBody' => '.container-items',
                'widgetItem' => '.item',
                'limit' => 20,
                'min' => 1,
                'insertButton' => '.add-item',
                'deleteButton' => '.remove-item',
                'model' => $modelAchievements[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'id',
                ],
            ]);
        ?>

        <div class="container-items"><!-- widgetContainer -->
            <div class="panel-title">
                <h5 class="panel-title pull-left">Достижения</h5><!-- widgetBody -->
                <div class="pull-right">
                    <button type="button" class="add-item btn btn-success btn-xs"><span class="glyphicon glyphicon-plus">+</span></button>
                </div>
            </div>
            <?php foreach ($modelAchievements as $i => $modelAchievement): ?>
                <div class="item panel panel-default"><!-- widgetBody -->
                    <div class="panel-heading">
                        <div class="pull-right">
                            <button type="button" class="remove-item btn btn-warning btn-xs"><span class="glyphicon glyphicon-minus">-</span></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class = "form-label">
                        <div class="panel-body">
                        <div class="row">
                            <div>
                                <div class="col-xs-6">
                                    <?= $form->field($modelAchievement, "[{$i}]act_participant_id")
                                        ->dropDownList(ArrayHelper::map($model->actsParticipantModel, 'id', 'actString'), ['prompt' => '---'])
                                        ->label('Акт участия');
                                    ?>
                                </div>
                                <div class="col-xs-4">
                                    <?= $form->field($modelAchievement, "[{$i}]achievement")->textInput()->label('Достижение'); ?>
                                </div>
                                <div class="col-xs-4">
                                    <?= $form->field($modelAchievement, "[{$i}]cert_number")->textInput()->label('Номер сертификата'); ?>
                                </div>

                                <div class="col-xs-4">
                                    <?= $form->field($modelAchievement, "[{$i}]date")->textInput(
                                        [
                                            'type' => 'date',
                                            'class' => 'form-control'
                                        ]
                                    )->label('Дата сертификата') ?>
                                </div>
                                <div class="col-xs-4" style="width: 30%; margin: auto;">
                                    <?php

                                    /*echo '<div class="toggle-wrapper form-group field-participantachievementwork-'.$i.'-type">
                                            <input type="hidden" value="0" id="participantachievementwork-'.$i.'-type" name="ParticipantAchievementWork['.$i.'][type]">
                                            <input type="checkbox" value="1" id="participantachievementwork-'.$i.'-type" class="toggle-checkbox" name="ParticipantAchievementWork['.$i.'][type]">
                                            <span class="toggle-icon off">Призер</span>
                                            <div class="toggle-container">
                                                <div class="toggle-button"></div>
                                            </div>
                                            <span class="toggle-icon on">Победитель</span>
                                            <div class="help-block"></div>
                                       </div>';*/


                                    echo '<div class="toggle-wrapper form-group field-participantachievementwork-'.$i.'-type">
                                                <span class="toggle-icon off" style="text-align: right">Призер</span>
                                                <div class="toggle-switcher">
                                                    <input type="hidden" value="0" name="ParticipantAchievementWork['.$i.'][type]">
                                                    <input type="checkbox" value="1" id="participantachievementwork-'.$i.'-type" class="toggle-checkbox" name="ParticipantAchievementWork['.$i.'][type]">
                                                    <label for="participantachievementwork-'.$i.'-type"></label>
                                                </div>
                                                <span class="toggle-icon on">Победитель</span>
                                            </div>';

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php DynamicFormWidget::end(); ?>
    </div>

    <div class="bordered-div">
        <div class="checkBlock">
            <?= $form->field($model, 'isBusinessTrip')->checkbox(['id' => 'tripCheckbox', 'onchange' => 'checkTrip()'])->label('Командировка') ?>

            <div id="divEscort" <?php echo !$model->orderBusinessTrip ? 'hidden' : '' ?>>
                <?= $form->field($model, 'escort')
                    ->dropDownList(
                        ArrayHelper::map($peoples,'id','fullFio'),
                        ['prompt' => 'не выбрано']
                    )->label('Сопровождающий');
                ?>
            </div>
            <div id="divOrderTrip" <?php echo !$model->orderBusinessTrip ? 'hidden' : '' ?>>
                <?= $form->field($model, 'orderBusinessTrip')
                    ->dropDownList(ArrayHelper::map($orders6,'id','fullName'), ['prompt' => '---'])->label('Приказ о командировке');
                ?>
            </div>
        </div>
    </div>

    <div class="col-xs-4">
        <label class="control-label">Приказ об участии</label>
        <select class="form-control" disabled>
            <option selected><?= $model->orderParticipant->getFullName() ?></option>
        </select>
    </div>

    <?= $form->field($model, 'addOrderParticipant')
        ->dropDownList(ArrayHelper::map($orders9,'id','fullName'), ['prompt' => '---'])
        ->label('Дополнительный приказ об участии');
    ?>

    <?= $form->field($model, 'keyWords')->textInput(['maxlength' => true])->label('Ключевые слова') ?>

    <?= $form->field($model, 'doc')->fileInput()->label('Документы о достижениях') ?>
    <?php if (strlen($model->docTable) > 10): ?>
        <?= $model->docTable; ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить',
            [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => 'Сохранить изменения? Если были загружены новые файлы заявок/достижений, то они заменят более старые',
                    'method' => 'post',
                ],
            ])
        ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>


<script>

    var counter = 1;

    function ClickBranch($this, $index)
    {
        if ($index == 4)
        {
            let parent = $this.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
            let childs = parent.querySelectorAll('.col-xs-4');
            let first_gen = childs[1].querySelectorAll('.form-group');
            let second_gen = first_gen[3].querySelectorAll('.form-control');
            if (second_gen[0].hasAttribute('disabled'))
                second_gen[0].removeAttribute('disabled');
            else
            {
                second_gen[0].value = 1;
                second_gen[0].setAttribute('disabled', 'disabled');
            }
        }
        
    }

    function checkTrip()
    {
        var chkBox = document.getElementById('tripCheckbox');
        if (chkBox.checked)
        {
            $("#divEscort").removeAttr("hidden");
            $("#divOrderTrip").removeAttr("hidden");
        }
        else
        {
            $("#divEscort").attr("hidden", "true");
            $("#divOrderTrip").attr("hidden", "true");
        }
    }
</script>

<?php
$js = <<< JS
    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {

        let elems = document.getElementsByClassName('base');
        
        let values = [];
        for (let i = 0; i < elems[0].children.length; i++)
            if (elems[1].children[i].childElementCount > 0)
                values[i] = elems[0].children[i].children[0].children[0].value;
        for (let j = 1; j < elems.length; j++)
            for (let i = 0; i < elems[1].children.length; i++)
                if (elems[j].children[i].childElementCount > 0)
                   elems[j].children[i].children[0].children[0].value = values[i]; 


    });

JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);

$js =<<< JS
    $(".dynamicform_wrapper1").on("afterInsert", function(e, item) {
        let elem = document.getElementsByClassName("date_achieve");
        let orig = document.getElementById('foreigneventwork-finish_date');
        elem[elem.length - 1].value = orig.value;
    });

JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);

/*let elem = document.getElementById('foreigneventparticipantsextended-0-allow_remote_id');
        if (elem.hasAttribute('disabled') && $(this).is(':checked') == true)
            elem.removeAttribute('disabled');
        if (!elem.hasAttribute('disabled') && $(this).is(':checked') == false)
        {
            elem.value = 1;
            elem.setAttribute('disabled', 'disabled');
        }*/
?>
