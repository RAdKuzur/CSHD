<?php

use common\models\work\ErrorsWork;
use common\models\work\UserWork;
use frontend\forms\ErrorsForm;
use frontend\models\work\rubac\PermissionTokenWork;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $user UserWork */
/* @var $form ErrorsForm */
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu', ['model' => $user]) ?>
    <div>
        <h3>Ошибки в учебных группах</h3>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $form->errorsGroup,
                'pagination' => false,
            ]),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['label' => 'Код ошибки', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->code;
                }],
                ['label' => 'Описание проблемы', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->description;
                }],
                ['label' => 'Место возникновения',
                    'format' => 'raw',
                    'value' => function(ErrorsWork $model) {
                    return Html::a($model->getEntityName(), [
                            'educational/'.str_replace('_', '-' , $model->table_name) . '/view',
                        'id' => $model->table_row_id
                    ]);
                }],
                ['label' => 'Отдел', 'value' =>
                function (ErrorsWork $model) {
                    return Yii::$app->branches->get($model->branch);
                }],

            ],
        ]); ?>
    </div>
    <div>
        <h3>Ошибки в образовательных программах</h3>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $form->errorsProgram,
                'pagination' => false,
            ]),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['label' => 'Код ошибки', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->code;
                }],
                ['label' => 'Описание проблемы', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->description;
                }],
                ['label' => 'Место возникновения',
                    'format' => 'raw',
                    'value' => function(ErrorsWork $model) {
                        return Html::a($model->getEntityName(), [
                            'educational/'.str_replace('_', '-' , $model->table_name) . '/view',
                            'id' => $model->table_row_id
                        ]);
                    }],
                ['label' => 'Отдел', 'value' =>
                    function (ErrorsWork $model) {
                        return Yii::$app->branches->get($model->branch);
                    }],

            ],
        ]); ?>
    </div>
    <div>
        <h3>Ошибки в приказах</h3>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $form->errorsOrder,
                'pagination' => false,
            ]),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['label' => 'Код ошибки', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->code;
                }],
                ['label' => 'Описание проблемы', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->description;
                }],
                ['label' => 'Место возникновения',
                    'format' => 'raw',
                    'value' => function(ErrorsWork $model) {
                        return Html::a($model->getEntityName(), [
                            'order/'.str_replace('_', '-' , $model->table_name) . '/view',
                            'id' => $model->table_row_id
                        ]);
                    }],
                ['label' => 'Отдел', 'value' =>
                    function (ErrorsWork $model) {
                        return Yii::$app->branches->get($model->branch);
                    }],

            ],
        ]); ?>
    </div>
    <div>
        <h3>Ошибки в мероприятиях</h3>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $form->errorsEvent,
                'pagination' => false,
            ]),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['label' => 'Код ошибки', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->code;
                }],
                ['label' => 'Описание проблемы', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->description;
                }],
                ['label' => 'Место возникновения',
                    'format' => 'raw',
                    'value' => function(ErrorsWork $model) {
                        return Html::a($model->getEntityName(), [
                            'order/'. 'our-event' . '/view',
                            'id' => $model->table_row_id
                        ]);
                    }],
                ['label' => 'Отдел', 'value' =>
                    function (ErrorsWork $model) {
                        return Yii::$app->branches->get($model->branch);
                    }],

            ],
        ]); ?>
    </div>
    <div>
        <h3>Ошибки в учёте достижений</h3>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $form->errorsAchievement,
                'pagination' => false,
            ]),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['label' => 'Код ошибки', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->code;
                }],
                ['label' => 'Описание проблемы', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->description;
                }],
                ['label' => 'Место возникновения',
                    'format' => 'raw',
                    'value' => function(ErrorsWork $model) {
                        return Html::a($model->getEntityName(), [
                            'event/'.str_replace('_', '-' , $model->table_name) . '/view',
                            'id' => $model->table_row_id
                        ]);
                    }],
                ['label' => 'Отдел', 'value' =>
                    function (ErrorsWork $model) {
                        return Yii::$app->branches->get($model->branch);
                    }],

            ],
        ]); ?>
    </div>
    <div>
        <h3>Ошибки в договорах</h3>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $form->errorsTreat,
                'pagination' => false,
            ]),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['label' => 'Код ошибки', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->code;
                }],
                ['label' => 'Описание проблемы', 'value' => function($model) {
                    return Yii::$app->errors->get($model->error)->description;
                }],
                ['label' => 'Место возникновения',
                    'format' => 'raw',
                    'value' => function(ErrorsWork $model) {
                        return Html::a($model->getEntityName(), [
                            'contract/'.str_replace('_', '-' , $model->table_name) . '/view', //?????????
                            'id' => $model->table_row_id
                        ]);
                    }],
                ['label' => 'Отдел', 'value' =>
                    function (ErrorsWork $model) {
                        return Yii::$app->branches->get($model->branch);
                    }],

            ],
        ]); ?>
    </div>
</div>
<div style="width:100%; height:1px; clear:both;"></div>