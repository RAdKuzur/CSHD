<?php

use common\components\dictionaries\base\RegulationTypeDictionary;
use common\helpers\DateFormatter;
use common\helpers\html\HtmlCreator;
use frontend\models\work\regulation\RegulationWork;
use kartik\export\ExportMenu;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \frontend\models\search\SearchRegulationEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = Yii::$app->regulationType->get(RegulationTypeDictionary::TYPE_EVENT);
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="regulation-event-index">
    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">
                    <?php

                    $gridColumns = [
                        ['attribute' => 'date', 'label' => 'Дата положения'],
                        ['attribute' => 'name', 'label' => 'Наименование'],
                        ['attribute' => 'orderName', 'label' => 'Приказ'],
                        ['attribute' => 'state', 'label' => 'Состояние'],
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

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'columns' => [
                ['attribute' => 'date',
                    'value' => function (RegulationWork $model) {
                        return DateFormatter::format($model->getDate(), DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
                    }
                ],
                ['attribute' => 'name'],
                ['attribute' => 'orderName'],
                ['attribute' => 'state', 'value' => function(RegulationWork $model){
                    return $model->getStates();
                }, 'format' => 'raw'],

                ['class' => ActionColumn::class],
            ],
            'rowOptions' => function ($model) {
                return ['data-href' => Url::to([Yii::$app->frontUrls::REG_EVENT_VIEW, 'id' => $model->id])];
            },
        ]); ?>
    </div>
</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>