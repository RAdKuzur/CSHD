<?php

use app\components\VerticalActionColumn;
use common\helpers\DateFormatter;
use common\helpers\html\HtmlCreator;
use frontend\models\search\SearchTrainingGroup;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel SearchTrainingGroup */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Учебные группы';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="training-group-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">

                    <?php
                        $gridColumns = [
                            ['attribute' => 'numberView', 'format' => 'html'],
                            ['attribute' => 'programName', 'format' => 'html'],
                            ['attribute' => 'branchName', 'label' => 'Отдел', 'format' => 'raw'],
                            ['attribute' => 'pureCountParticipants', 'label' => 'Кол-во детей'],
                            ['attribute' => 'teachersList', 'format' => 'html'],
                            'start_date',
                            'finish_date',
                            ['attribute' => 'budgetText', 'label' => 'Бюджет', 'filter' => [ 1 => "Бюджет", 0 => "Внебюджет"]],
                        ];

                        echo ExportMenu::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => $gridColumns,
                            'options' => [
                                'padding-bottom: 100px',
                            ]
                        ]);
                    ?>

                </div>
            </div>

            <?= HtmlCreator::filterToggle() ?>
        </div>
    </div>

    <?= $this->render('_search', ['searchModel' => $searchModel]) ?>

    <div style="margin-bottom: 10px">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,

        'columns' => [
            ['attribute' => 'numberPretty', 'format' => 'raw'],
            ['attribute' => 'teachersList', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'branchString', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'programName', 'encodeLabel' => false, 'label' => 'Образовательная<br>программа'],
            ['attribute' => 'start_date',
                'value' => function(TrainingGroupWork $model){
                    return DateFormatter::format($model->start_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
                },
                'encodeLabel' => false,
            ],
            ['attribute' => 'finish_date',
                'value' => function(TrainingGroupWork $model){
                    return DateFormatter::format($model->finish_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
                },
                'encodeLabel' => false,
            ],
            ['attribute' => 'budgetString'],

            ['class' => VerticalActionColumn::class,
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action == 'update') {
                        return Url::toRoute(['educational/training-group/' . 'base-form', 'id' => $model->id]);
                    }
                    return Url::toRoute(['educational/training-group/' . $action, 'id' => $model->id]);
                }
            ],
        ],
        'rowOptions' => function ($model) {
            return ['data-href' => Url::to([Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $model->id])];
        },
    ]); ?>

    </div>
</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>