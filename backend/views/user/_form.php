<?php

use backend\models\forms\UserForm;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserForm */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 25px;
    }

    .grid-item {
        display: flex;
        justify-content: left;
        align-items: center;
    }

    .btn-template {
        width: 420px;
        height: 40px;
    }
</style>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model->entity, 'firstname')->textInput()->label('Имя') ?>
    <?= $form->field($model->entity, 'surname')->textInput()->label('Фамилия') ?>
    <?= $form->field($model->entity, 'patronymic')->textInput()->label('Отчество') ?>
    <?= $form->field($model->entity, 'username')->textInput()->label('Логин') ?>

    <?php if (is_null($model->entity->password_hash)): ?>
        <?= $form->field($model->entity, 'password_hash')->textInput()->label('Пароль'); ?>
    <?php endif; ?>

    <?= $form->field($model->entity, 'aka')->widget(Select2::classname(), [
        'data' => ArrayHelper::map($model->peoples, 'id', 'fullFio'),
        'size' => Select2::LARGE,
        'options' => ['prompt' => '---'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label('Также является'); ?>

    <hr>
    <h5>Правила доступа</h5>
    <div class="grid-container">
        <div class="grid-item"><button id="button-teacher" class="btn btn-secondary btn-template">Шаблон <b>"Педагог"</b></button></div>
        <div class="grid-item"><button class="btn btn-secondary btn-template" hidden></button></div>
        <div class="grid-item"><button class="btn btn-secondary btn-template" hidden></button></div>
        <div class="grid-item"><button id="button-study" class="btn btn-secondary btn-template">Шаблон <b>"Информатор по учебной деятельности"</b></button></div>
        <div class="grid-item"><button id="button-event" class="btn btn-secondary btn-template">Шаблон <b>"Информатор по мероприятиям"</b></button></div>
        <div class="grid-item"><button id="button-document" class="btn btn-secondary btn-template">Шаблон <b>"Информатор по документообороту"</b></button></div>
        <div class="grid-item"><button id="button-branch-controller" class="btn btn-secondary btn-template">Шаблон <b>"Контролер в отделе"</b></button></div>
        <div class="grid-item"><button id="button-super-controller" class="btn btn-secondary btn-template">Шаблон <b>"Суперконтролер"</b></button></div>
        <div class="grid-item"><button id="button-admin" class="btn btn-secondary btn-template">Шаблон <b>"Администратор"</b></button></div>
    </div>

    <?= $form->field($model, 'userPermissions')->checkboxList(
        ArrayHelper::map($model->permissions, 'id', 'name'),
        [
            'class' => 'base',
            'item' => function ($index, $label, $name, $checked, $value) use ($model) {
                if ($checked == 1) {
                    $checked = 'checked';
                }
                return
                    '<div class="checkbox" class="form-control">
                            <label style="margin-bottom: 0px" for="permission-' . $model->permissions[$index]->short_code .'">
                                <input id="permission-'. $model->permissions[$index]->short_code .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                                '. $label .'
                            </label>
                        </div>';
            }
        ]
    )->label(false)
    ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$permissionsTeacher = json_encode(Yii::$app->rubac->getTeacherPermissions());
$permissionsStudyInform = json_encode(Yii::$app->rubac->getStudyInformantPermissions());
$permissionsEventInform = json_encode(Yii::$app->rubac->getEventInformantPermissions());
$permissionsDocInform = json_encode(Yii::$app->rubac->getDocumentInformantPermissions());
$permissionsBranchController = json_encode(Yii::$app->rubac->getBranchControllerPermissions());
$permissionsSuperController = json_encode(Yii::$app->rubac->getSuperControllerPermissions());
$permissionsAdmin = json_encode(Yii::$app->rubac->getAdminPermissions());

$this->registerJs(<<<JS
    function activateCheckboxes(permissions) {
        let checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(function(checkbox) {
            if (checkbox.id.startsWith('permission-')) {
                checkbox.checked = false;
            }
        });
        
        permissions.forEach(function(permission) {
            let checkboxId = 'permission-' + permission;
            let checkbox = document.getElementById(checkboxId);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    }

    document.getElementById('button-teacher').addEventListener('click', function() {
        activateCheckboxes($permissionsTeacher);
    });
    document.getElementById('button-study').addEventListener('click', function() {
        activateCheckboxes($permissionsStudyInform);
    });
    document.getElementById('button-event').addEventListener('click', function() {
        activateCheckboxes($permissionsEventInform);
    });
    document.getElementById('button-document').addEventListener('click', function() {
        activateCheckboxes($permissionsDocInform);
    });
    document.getElementById('button-branch-controller').addEventListener('click', function() {
        activateCheckboxes($permissionsBranchController);
    });
    document.getElementById('button-super-controller').addEventListener('click', function() {
        activateCheckboxes($permissionsSuperController);
    });
    document.getElementById('button-admin').addEventListener('click', function() {
        activateCheckboxes($permissionsAdmin);
    });
JS
    , $this::POS_LOAD);
?>