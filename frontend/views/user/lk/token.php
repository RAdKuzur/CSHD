<?php

use common\models\work\UserWork;
use frontend\models\work\rubac\PermissionTokenWork;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model */
/* @var $user UserWork */
/* @var $users */
/* @var $permissions */
/* @var $activeTokens */
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu', ['model' => $user]) ?>
    <h3>Выдать токен доступа</h3>
    <?php $form = ActiveForm::begin(); ?>
    <?php
        $params = [
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'user')
            ->dropDownList(ArrayHelper::map($users, 'id', 'fullName'), $params)
            ->label('Пользователь');
        echo $form
            ->field($model, 'permission')
            ->dropDownList(ArrayHelper::map($permissions, 'id', 'name'), $params)
            ->label('Разрешение');
        ?>
    <br>
    <div class = "time-token">
        <h3>Время жизни токена</h3>
        <?= $form->field($model, 'week')->textInput()->label('Недели');?>
        <?= $form->field($model, 'day')->textInput()->label('Дни');?>
        <?= $form->field($model, 'hour')->textInput()->label('Часы');?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Выдать токен', ['class' => 'btn btn-success']) ?>
    </div>
    <?php $form = ActiveForm::end(); ?>
    <div class="token-container" style="float: left">
        <h3>Активные токены</h3>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $activeTokens,
                'pagination' => false,
            ]),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['label' => 'Разрешение', 'value' => function(PermissionTokenWork $model) {
                    return $model->permissionWork->name;
                }],
                'start_time',
                'end_time',
            ],
        ]) ?>
    </div>
</div>
<div style="width:100%; height:1px; clear:both;"></div>