<?php

use common\helpers\DateFormatter;
use common\models\work\ErrorsWork;
use frontend\forms\analytics\AnalyticErrorForm;
use yii\bootstrap5\Tabs;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use common\helpers\html\HtmlCreator;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $model AnalyticErrorForm */
/* @var $searchModel SearchErrors */ 
/* @var $branches array */ 

$activeTab = Yii::$app->request->get('tab', 'groups');
$id = Yii::$app->request->get('id');

$this->title = 'Ошибки заполнения';
$this->params['breadcrumbs'][] = $this->title;


$gridColumns = [
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
];

?>


<?php
$tabsConfig = [
    'groups' => [
        'label' => 'Учебные группы',
        'data' => $model->groupErrors,
    ],
    'programs' => [
        'label' => 'Образовательные программы',
        'data' => $model->programErrors,
    ],
    'orders' => [
        'label' => 'Приказы',
        'data' => $model->orderErrors,
    ],
    'events' => [
        'label' => 'Мероприятия',
        'data' => $model->eventErrors,
    ],
    'achievements' => [
        'label' => 'Учёт достижений',
        'data' => $model->foreignEventErrors,
    ],
];

$exportColumns = $gridColumns;

foreach ($exportColumns as &$column) {
    if (isset($column['attribute']) && $column['attribute'] === 'entity_name') {
        $column['value'] = function (ErrorsWork $model) {
            return $model->getEntityNameForExport();
        };
        // Убираем 'raw' — для формулы он не нужен
        unset($column['format']);
        break;
    }
}
unset($column);


$exportModels = $tabsConfig[$activeTab]['data'] ?? [];
$exportDataProvider = new ArrayDataProvider([
    'allModels' => $exportModels,
    'pagination' => false,
]);
?>

<div style="width:100%; height:1px; clear:both;"></div>

<div class="analytics-errors">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space" style="margin-bottom: 15px;">
            <div class="flexx">
                <div class="export-menu">
                    <?= ExportMenu::widget([
                        'dataProvider' => $exportDataProvider,
                        'columns' => $exportColumns,
                    ]); ?>
                </div>
            </div>
            <?= HtmlCreator::filterToggle() ?>
        </div>
    </div>

    <?= $this->render('_search_errors', [
        'searchModel' => $searchModel,
        'branches' => $branches,
        'activeTab' => $activeTab,
    ]) ?>

    <?php
    $tabsItems = [];

    foreach ($tabsConfig as $key => $tab) {

        $data = $tab['data'];

        $content = !empty($data)
            ? GridView::widget([
                'summary' => false,
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $data,
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
                'columns' => $gridColumns,
            ])
            : '<div class="alert alert-info">Ошибки в данной вкладке не были найдены</div>';

        $tabsItems[] = [
            'label' => $tab['label'],
            'content' => $content,
            'active' => $activeTab === $key,
            'linkOptions' => ['data-tab' => $key],
        ];
    }
    ?>

    <?= Tabs::widget([
        'items' => $tabsItems,
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