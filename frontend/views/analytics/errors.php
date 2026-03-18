<?php

use common\helpers\DateFormatter;
use common\models\work\ErrorsWork;
use frontend\forms\analytics\AnalyticErrorForm;
use yii\bootstrap5\Tabs;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use common\helpers\html\HtmlCreator;

/* @var $this yii\web\View */
/* @var $model AnalyticErrorForm */
/* @var $searchModel SearchErrors */ 
/* @var $branches array */ 

$activeTab = Yii::$app->request->get('tab', 'groups');
$id = Yii::$app->request->get('id');

$this->title = 'Ошибки заполнения';
$this->params['breadcrumbs'][] = $this->title;
?>

<div style="width:100%; height:1px; clear:both;"></div>

<div class="analytics-errors">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="flexx space" style="margin-bottom: 15px;">
        <div class="flexx">

        </div>
        <?= HtmlCreator::filterToggle() ?>
    </div>

    <?= $this->render('_search_errors', [
        'searchModel' => $searchModel,
        'branches' => $branches,
        'activeTab' => $activeTab,
    ]) ?>

    <?= Tabs::widget([
        'items' => [
            [
                'label' => 'Учебные группы',
                'content' => !empty($model->groupErrors) ? GridView::widget([
                    'summary' => false,
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $model->groupErrors,
                        'pagination' => false,
                        'sort' => [
                            'attributes' => [
                                'error_code',
                                'error_description',
                                'create_datetime',
                                'entity_name',
                                'branch',
                            ],
                        ],
                    ]),
                    'columns' => [
                        [
                            'attribute' => 'error_code',
                            'label' => 'Код ошибки',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->errors->get($model->error)->code;
                            }
                        ],
                        [
                            'attribute' => 'error_description',
                            'label' => 'Описание проблемы',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->errors->get($model->error)->description;
                            }
                        ],
                        [
                            'attribute' => 'create_datetime',
                            'label' => 'Дата и время',
                            'value' => function(ErrorsWork $model) {
                                $date = explode(' ', $model->create_datetime)[0];
                                $time = explode(' ', $model->create_datetime)[1];
                                return 
                                    DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dm_dot) . ' в ' .
                                    DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
                            }
                        ],
                        [
                            'attribute' => 'entity_name',
                            'label' => 'Место возникновения',
                            'format' => 'raw',
                            'value' => function(ErrorsWork $model) {
                                return Html::a($model->getEntityName(), [
                                    'educational/'.str_replace('_', '-' , $model->table_name) . '/view',
                                    'id' => $model->table_row_id
                                ]);
                            }
                        ],
                        [
                            'attribute' => 'branch',
                            'label' => 'Отдел',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->branches->get($model->branch);
                            }
                        ],
                    ],
                ]) : '<div class="alert alert-info">Ошибки в данной вкладке не были найдены</div>',
                'active' => $activeTab === 'groups',
                'linkOptions' => ['data-tab' => 'groups'],
            ],
            [
                'label' => 'Образовательные программы',
                'content' => !empty($model->programErrors) ? GridView::widget([
                    'summary' => false,
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $model->programErrors,
                        'pagination' => false,
                        'sort' => [
                            'attributes' => [
                                'error_code',
                                'error_description',
                                'create_datetime',
                                'entity_name',
                                'branch',
                            ],
                        ],
                    ]),
                    'columns' => [
                        [
                            'attribute' => 'error_code',
                            'label' => 'Код ошибки',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->errors->get($model->error)->code;
                            }
                        ],
                        [
                            'attribute' => 'error_description',
                            'label' => 'Описание проблемы',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->errors->get($model->error)->description;
                            }
                        ],
                        [
                            'attribute' => 'create_datetime',
                            'label' => 'Дата и время',
                            'value' => function(ErrorsWork $model) {
                                $date = explode(' ', $model->create_datetime)[0];
                                $time = explode(' ', $model->create_datetime)[1];
                                return 
                                    DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dm_dot) . ' в ' .
                                    DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
                            }
                        ],
                        [
                            'attribute' => 'entity_name',
                            'label' => 'Место возникновения',
                            'format' => 'raw',
                            'value' => function(ErrorsWork $model) {
                                return Html::a($model->getEntityName(), [
                                    'educational/'.str_replace('_', '-' , $model->table_name) . '/view',
                                    'id' => $model->table_row_id
                                ]);
                            }
                        ],
                        [
                            'attribute' => 'branch',
                            'label' => 'Отдел',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->branches->get($model->branch);
                            }
                        ],
                    ],
                ]) : '<div class="alert alert-info">Ошибки в данной вкладке не были найдены</div>',
                'active' => $activeTab === 'programs',
                'linkOptions' => ['data-tab' => 'programs'],
            ],
            [
                'label' => 'Приказы',
                'content' => !empty($model->orderErrors) ? GridView::widget([
                    'summary' => false,
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $model->orderErrors,
                        'pagination' => false,
                        'sort' => [
                            'attributes' => [
                                'error_code',
                                'error_description',
                                'create_datetime',
                                'entity_name',
                                'branch',
                            ],
                        ],
                    ]),
                    'columns' => [
                        [
                            'attribute' => 'error_code',
                            'label' => 'Код ошибки',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->errors->get($model->error)->code;
                            }
                        ],
                        [
                            'attribute' => 'error_description',
                            'label' => 'Описание проблемы',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->errors->get($model->error)->description;
                            }
                        ],
                        [
                            'attribute' => 'create_datetime',
                            'label' => 'Дата и время',
                            'value' => function(ErrorsWork $model) {
                                $date = explode(' ', $model->create_datetime)[0];
                                $time = explode(' ', $model->create_datetime)[1];
                                return 
                                    DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dm_dot) . ' в ' .
                                    DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
                            }
                        ],
                        [
                            'attribute' => 'entity_name',
                            'label' => 'Место возникновения',
                            'format' => 'raw',
                            'value' => function(ErrorsWork $model) {
                                return Html::a($model->getEntityName(), [
                                    'order/'.str_replace('_', '-' , $model->table_name) . '/view',
                                    'id' => $model->table_row_id
                                ]);
                            }
                        ],
                        [
                            'attribute' => 'branch',
                            'label' => 'Отдел',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->branches->get($model->branch);
                            }
                        ],
                    ],
                ]) : '<div class="alert alert-info">Ошибки в данной вкладке не были найдены</div>',
                'active' => $activeTab === 'orders',
                'linkOptions' => ['data-tab' => 'orders'],
            ],
            [
                'label' => 'Мероприятия',
                'content' => !empty($model->eventErrors) ? GridView::widget([
                    'summary' => false,
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $model->eventErrors,
                        'pagination' => false,
                        'sort' => [
                            'attributes' => [
                                'error_code',
                                'error_description',
                                'create_datetime',
                                'entity_name',
                                'branch',
                            ],
                        ],
                    ]),
                    'columns' => [
                        [
                            'attribute' => 'error_code',
                            'label' => 'Код ошибки',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->errors->get($model->error)->code;
                            }
                        ],
                        [
                            'attribute' => 'error_description',
                            'label' => 'Описание проблемы',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->errors->get($model->error)->description;
                            }
                        ],
                        [
                            'attribute' => 'create_datetime',
                            'label' => 'Дата и время',
                            'value' => function(ErrorsWork $model) {
                                $date = explode(' ', $model->create_datetime)[0];
                                $time = explode(' ', $model->create_datetime)[1];
                                return 
                                    DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dm_dot) . ' в ' .
                                    DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
                            }
                        ],
                        [
                            'attribute' => 'entity_name',
                            'label' => 'Место возникновения',
                            'format' => 'raw',
                            'value' => function(ErrorsWork $model) {
                                return Html::a($model->getEntityName(), [
                                    'order/'. 'our-event' . '/view',
                                    'id' => $model->table_row_id
                                ]);
                            }
                        ],
                        [
                            'attribute' => 'branch',
                            'label' => 'Отдел',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->branches->get($model->branch);
                            }
                        ],
                    ],
                ]) : '<div class="alert alert-info">Ошибки в данной вкладке не были найдены</div>',
                'active' => $activeTab === 'events',
                'linkOptions' => ['data-tab' => 'events'],
            ],
            [
                'label' => 'Учёт достижений',
                'content' => !empty($model->foreignEventErrors) ? GridView::widget([
                    'summary' => false,
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $model->foreignEventErrors,
                        'pagination' => false,
                        'sort' => [
                            'attributes' => [
                                'error_code',
                                'error_description',
                                'create_datetime',
                                'entity_name',
                                'branch',
                            ],
                        ],
                    ]),
                    'columns' => [
                        [
                            'attribute' => 'error_code',
                            'label' => 'Код ошибки',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->errors->get($model->error)->code;
                            }
                        ],
                        [
                            'attribute' => 'error_description',
                            'label' => 'Описание проблемы',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->errors->get($model->error)->description;
                            }
                        ],
                        [
                            'attribute' => 'create_datetime',
                            'label' => 'Дата и время',
                            'value' => function(ErrorsWork $model) {
                                $date = explode(' ', $model->create_datetime)[0];
                                $time = explode(' ', $model->create_datetime)[1];
                                return 
                                    DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::dm_dot) . ' в ' .
                                    DateFormatter::format($time, DateFormatter::His_colon, DateFormatter::Hi_colon);
                            }
                        ],
                        [
                            'attribute' => 'entity_name',
                            'label' => 'Место возникновения',
                            'format' => 'raw',
                            'value' => function(ErrorsWork $model) {
                                return Html::a($model->getEntityName(), [
                                    'event/'.str_replace('_', '-' , $model->table_name) . '/view',
                                    'id' => $model->table_row_id
                                ]);
                            }
                        ],
                        [
                            'attribute' => 'branch',
                            'label' => 'Отдел',
                            'value' => function(ErrorsWork $model) {
                                return Yii::$app->branches->get($model->branch);
                            }
                        ],
                    ],
                ]) : '<div class="alert alert-info">Ошибки в данной вкладке не были найдены</div>',
                'active' => $activeTab === 'achievements',
                'linkOptions' => ['data-tab' => 'achievements'],
            ],
        ],
    ]); ?>
</div>

<div style="width:100%; height:1px; clear:both;"></div>

<?php
$js = <<<JS
    $('#filterToggle').on('click', function() {
        $(this).toggleClass('active');
        $('#filterPanel').toggleClass('active');
    });

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {

        let tab = $(e.target).data('tab');

        if (!tab) return;

        let url = new URL(window.location.href);
        url.searchParams.set('tab', tab);

        window.history.replaceState({}, '', url);

        $('#activeTabInput').val(tab);
    });

    $(document).on('click', 'a[data-sort]', function(e) {
    e.preventDefault();
    let url = new URL($(this).attr('href'), window.location.origin);
    url.searchParams.set('tab', $('#activeTabInput').val());
    window.location.href = url.toString();
});
JS;
$this->registerJs($js, yii\web\View::POS_READY);
?>