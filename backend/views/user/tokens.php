<?php

use backend\forms\TokensForm;
use common\helpers\DateFormatter;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use frontend\models\work\rubac\PermissionTokenWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TokensForm */

$this->title = 'Выдача токенов';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <div class="content-container">
        <h3>Выдать токен доступа</h3>
        <br>

        <?php $form = ActiveForm::begin(); ?>

        <?=
        $form->field($model, 'userId')->dropDownList(
            ArrayHelper::map($model->users, 'id', 'fullName')
        )->label('Пользователь');
        ?>

        <?=
        $form->field($model, 'permissionId')->dropDownList(
            ArrayHelper::map($model->permissions, 'id', 'name')
        )->label('Разрешение');
        ?>

        <?=
        $form->field($model, 'branch')->dropDownList(
            Yii::$app->branches->getList(), ['prompt' => '---']
        )->label('Отдел (при необходимости')
        ?>

        <div class="col-xs-12" style="padding-left: 0">
            <div class="col-xs-3" style="padding-left: 0">
                <h4>Время жизни токена</h4>
            </div>

            <div class="col-xs-3">
                <?= $form->field($model, 'duration')->textInput(['type' => 'number', 'style' => 'max-width: 100px', 'value' => 0])->label('Часы') ?>
            </div>

        </div>
        <div class="panel-body" style="padding: 0; margin: 0"></div>

        <div class="form-group">
            <?= Html::submitButton('Выдать токен', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <br>

    <h3>Активные токены</h3>
    <table class="table table-striped">
        <tr>
            <th>ФИО пользователя</th>
            <th>Разрешение</th>
            <th>Дата выдачи</th>
            <th>Дата окончания</th>
            <th></th>
        </tr>
        <?php
        /** @var PermissionTokenWork $token */
        foreach ($model->tokens as $token) : ?>
            <tr>
                <td><?= $token->userWork->getFullName() ?></td>
                <td><?= $token->permissionWork->name ?></td>
                <td><?= $token->start_time ?></td>
                <td><?= $token->end_time ?></td>
                <td><?= Html::a('Отозвать токен', Url::to(['delete-token', 'id' => $token->id]), ['class' => 'btn btn-danger']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</div>
<div style="width:100%; height:1px; clear:both;"></div>