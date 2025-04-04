<?php

use app\components\VerticalActionColumn;
use common\helpers\DateFormatter;
use common\helpers\html\HtmlCreator;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\search\SearchForeignEvent;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel SearchForeignEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Учет достижений в мероприятиях';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="foreign-event-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">

                <div class="export-menu" style="margin: auto 0">
                    <?php

                    $gridColumns = [
                        ['attribute' => 'name'],
                        ['attribute' => 'companyString', 'label' => 'Организатор', 'value' => function (ForeignEventWork $model) {
                            return $model->organizerWork->name;
                        }],
                        ['attribute' => 'begin_date', 'label' => 'Дата<br>начала', 'encodeLabel' => false, 'value' => function (ForeignEventWork $model) {
                            return DateFormatter::format($model->begin_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
                        }],
                        ['attribute' => 'end_date', 'label' => 'Дата<br>окончания', 'encodeLabel' => false, 'value' => function (ForeignEventWork $model) {
                            return DateFormatter::format($model->end_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
                        }],
                        ['attribute' => 'city', 'label' => 'Город'],
                        ['attribute' => 'eventWayString', 'label' => 'Формат<br>проведения', 'encodeLabel' => false, 'value' => function(ForeignEventWork $model){
                            return Yii::$app->eventWay->get($model->format);
                        }],
                        ['attribute' => 'eventLevelString', 'label' => 'Уровень', 'value' => function(ForeignEventWork $model){
                            return Yii::$app->eventLevel->get($model->level);
                        }],

                        ['attribute' => 'teachers', 'label' => 'Педагоги','value' => function(ForeignEventWork $model){
                            return $model->getTeachers(ForeignEventWork::VIEW_TYPE);
                        }, 'format' => 'raw'],

                        ['attribute' => 'participantCount', 'format' => 'raw', 'label' => 'Кол-во<br>участников', 'encodeLabel' => false,
                            'value' => function (ForeignEventWork $model) {
                                return count($model->actParticipantWorks);
                            }
                        ],
                        ['attribute' => 'winners', 'label' => 'Победители', 'value' => function(ForeignEventWork $model){
                            return $model->getWinners();
                        }],
                        ['attribute' => 'prizes', 'label' => 'Призёры', 'value' => function(ForeignEventWork $model){
                            return $model->getPrizes();
                        }],
                        ['attribute' => 'businessTrips', 'label' => 'Командировка', 'value' => function(ForeignEventWork $model){
                            return $model->isTrip();
                        }],
                    ];

                    echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $gridColumns,

                        'options' => [
                            'padding-bottom: 100px',
                        ],
                    ]);

                    ?>
                </div>
            </div>

            <?= HtmlCreator::filterToggle() ?>
        </div>
    </div>

    <?= $this->render('_search', ['searchModel' => $searchModel]) ?>

    <div style="margin-bottom: 10px">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            ['attribute' => 'name'],
            ['attribute' => 'companyString', 'label' => 'Организатор', 'value' => function (ForeignEventWork $model) {
                return $model->organizerWork->name;
            }],
            ['attribute' => 'begin_date', 'label' => 'Дата<br>начала', 'encodeLabel' => false, 'value' => function (ForeignEventWork $model) {
                return DateFormatter::format($model->begin_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
            }],
            ['attribute' => 'end_date', 'label' => 'Дата<br>окончания', 'encodeLabel' => false, 'value' => function (ForeignEventWork $model) {
                return DateFormatter::format($model->end_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
            }],
            ['attribute' => 'city', 'label' => 'Город'],
            ['attribute' => 'eventWayString', 'label' => 'Формат<br>проведения', 'encodeLabel' => false, 'value' => function(ForeignEventWork $model){
                return Yii::$app->eventWay->get($model->format);
            }],
            ['attribute' => 'eventLevelString', 'label' => 'Уровень', 'value' => function(ForeignEventWork $model){
                return Yii::$app->eventLevel->get($model->level);
            }],

            ['attribute' => 'teachers', 'label' => 'Педагоги','value' => function(ForeignEventWork $model){
                return $model->getTeachers(ForeignEventWork::VIEW_TYPE);
            }, 'format' => 'raw'],

            ['attribute' => 'participantCount', 'format' => 'raw', 'label' => 'Кол-во<br>участников', 'encodeLabel' => false,
                'value' => function (ForeignEventWork $model) {
                    return count($model->actParticipantWorks);
                }
            ],
            ['attribute' => 'winners', 'label' => 'Победители', 'value' => function(ForeignEventWork $model){
                return $model->getWinners();
            }],
            ['attribute' => 'prizes', 'label' => 'Призёры', 'value' => function(ForeignEventWork $model){
                return $model->getPrizes();
            }],

            ['class' => VerticalActionColumn::class],
        ],
        'rowOptions' => function ($model) {
            return ['data-href' => Url::to([Yii::$app->frontUrls::FOREIGN_EVENT_VIEW, 'id' => $model->id])];
        },
    ]); ?>


</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>