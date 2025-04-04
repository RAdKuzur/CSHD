<?php

use common\helpers\DateFormatter;
use common\models\work\ErrorsWork;
use frontend\forms\analytics\AnalyticErrorForm;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model AnalyticErrorForm */

?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <div>
        <div class="substrate">
            <h3>Ошибки в учебных группах</h3>
        </div>

        <?= GridView::widget([
                'summary' => false,
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $model->groupErrors,
                    'pagination' => false,
                ]),
                'columns' => [
                    ['label' => 'Код ошибки', 'value' => function(ErrorsWork $model) {
                        return Yii::$app->errors->get($model->error)->code;
                    }],
                    ['label' => 'Описание проблемы', 'value' => function(ErrorsWork $model) {
                        return Yii::$app->errors->get($model->error)->description;
                    }],
                    ['label' => 'Дата и время', 'value' => function(ErrorsWork $model) {
                        $date = explode(' ', $model->create_datetime)[0];
                        $time = explode(' ', $model->create_datetime)[1];
                        return
                            DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dm_dot) . ' в ' .
                            DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
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
            'summary' => false,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $model->programErrors,
                'pagination' => false,
            ]),
            'columns' => [
                ['label' => 'Код ошибки', 'value' => function(ErrorsWork $model) {
                    return Yii::$app->errors->get($model->error)->code;
                }],
                ['label' => 'Описание проблемы', 'value' => function(ErrorsWork $model) {
                    return Yii::$app->errors->get($model->error)->description;
                }],
                ['label' => 'Дата и время', 'value' => function(ErrorsWork $model) {
                    $date = explode(' ', $model->create_datetime)[0];
                    $time = explode(' ', $model->create_datetime)[1];
                    return
                        DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dm_dot) . ' в ' .
                        DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
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
            'summary' => false,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $model->orderErrors,
                'pagination' => false,
            ]),
            'columns' => [
                ['label' => 'Код ошибки', 'value' => function(ErrorsWork $model) {
                    return Yii::$app->errors->get($model->error)->code;
                }],
                ['label' => 'Описание проблемы', 'value' => function(ErrorsWork $model) {
                    return Yii::$app->errors->get($model->error)->description;
                }],
                ['label' => 'Дата и время', 'value' => function(ErrorsWork $model) {
                    $date = explode(' ', $model->create_datetime)[0];
                    $time = explode(' ', $model->create_datetime)[1];
                    return
                        DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dm_dot) . ' в ' .
                        DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
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
            'summary' => false,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $model->eventErrors,
                'pagination' => false,
            ]),
            'columns' => [
                ['label' => 'Код ошибки', 'value' => function(ErrorsWork $model) {
                    return Yii::$app->errors->get($model->error)->code;
                }],
                ['label' => 'Описание проблемы', 'value' => function(ErrorsWork $model) {
                    return Yii::$app->errors->get($model->error)->description;
                }],
                ['label' => 'Дата и время', 'value' => function(ErrorsWork $model) {
                    $date = explode(' ', $model->create_datetime)[0];
                    $time = explode(' ', $model->create_datetime)[1];
                    return
                        DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dm_dot) . ' в ' .
                        DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
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
            'summary' => false,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $model->foreignEventErrors,
                'pagination' => false,
            ]),
            'columns' => [
                ['label' => 'Код ошибки', 'value' => function(ErrorsWork $model) {
                    return Yii::$app->errors->get($model->error)->code;
                }],
                ['label' => 'Описание проблемы', 'value' => function(ErrorsWork $model) {
                    return Yii::$app->errors->get($model->error)->description;
                }],
                ['label' => 'Дата и время', 'value' => function(ErrorsWork $model) {
                    $date = explode(' ', $model->create_datetime)[0];
                    $time = explode(' ', $model->create_datetime)[1];
                    return
                        DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dm_dot) . ' в ' .
                        DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
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
</div>
<div style="width:100%; height:1px; clear:both;"></div>
