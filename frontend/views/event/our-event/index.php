<?php

use app\components\VerticalActionColumn;
use common\helpers\html\HtmlCreator;
use frontend\models\search\SearchEvent;
use frontend\models\work\event\EventWork;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel SearchEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Мероприятия';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">
                    <?php

                    $gridColumns = [
                        ['attribute' => 'name'],
                        ['attribute' => 'start_date'],
                        ['attribute' => 'finish_date'],
                        ['attribute' => 'address'],
                        ['attribute' => 'eventType'],
                        ['attribute' => 'eventLevel'],
                        ['attribute' => 'eventWay'],
                        ['attribute' => 'ageRestrictions'],
                        ['attribute' => 'scopesSplitter', 'label' => 'Тематическая направленность'],
                        ['attribute' => 'childParticipantsCount'],
                        ['attribute' => 'childRSTParticipantsCount'],
                        ['attribute' => 'teacherParticipantsCount'],
                        ['attribute' => 'otherParticipantsCount'],
                        ['attribute' => 'participantCount'],
                        ['attribute' => 'is_federal', 'value' => function(EventWork $model){
                            if ($model->is_federal == 1) {
                                return 'Да';
                            }
                            else{
                                return 'Нет';
                            }
                        }, 'filter' => [1 => "Да", 0 => "Нет"]],
                        ['attribute' => 'responsibleString', 'label' => 'Ответственный(-ые) работник(-и)'],
                        ['attribute' => 'eventBranches', 'label' => 'Мероприятие проводит', 'format' => 'raw'],
                        ['attribute' => 'orderStringRaw', 'format' => 'raw', 'label' => 'Приказ'],
                        'eventWayString',
                        ['attribute' => 'regulationRaw', 'label' => 'Положение', 'format' => 'raw'],
                        ['attribute' => 'eventGroupRaw', 'label' => 'Связанные группы', 'format' => 'raw'],

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
            ['attribute' => 'name', 'encodeLabel' => false, 'label' => 'Название<br>мероприятия'],
            ['attribute' => 'datePeriod', 'encodeLabel' => false, 'label' => 'Период<br>проведения'],
            ['attribute' => 'eventLevelAndType', 'label' => 'Уровень и Тип<br>мероприятия', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'address', 'label' => 'Адрес<br>проведения', 'encodeLabel' => false],
            ['attribute' => 'participantCount', 'label' => 'Кол-во<br>участников', 'encodeLabel' => false],
            ['attribute' => 'orderNameRaw', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'eventWay', 'label' => 'Формат<br>проведения', 'encodeLabel' => false],
            ['attribute' => 'regulationRaw', 'encodeLabel' => false, 'format' => 'raw'],

            ['class' => VerticalActionColumn::class],
        ],
        'rowOptions' => function ($model) {
            return ['data-href' => Url::to([Yii::$app->frontUrls::OUR_EVENT_VIEW, 'id' => $model->id])];
        },
    ]); ?>
    </div>
</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>