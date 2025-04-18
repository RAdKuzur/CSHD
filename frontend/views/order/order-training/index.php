<?php

use app\components\VerticalActionColumn;
use common\helpers\DateFormatter;
use common\helpers\html\HtmlCreator;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\order\OrderTrainingWork;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \frontend\models\search\SearchOrderTraining */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Приказы по образовательной деятельности';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
$tempArchive = $session->get("archiveIn");
?>
<div class="order-training-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">
                    <?php

                    $gridColumns = [
                        ['attribute' => 'fullNumber'],
                        ['attribute' => 'orderDate', 'encodeLabel' => false],
                        ['attribute' => 'orderName', 'encodeLabel' => false],
                        ['attribute' => 'bringName', 'encodeLabel' => false],
                        ['attribute' => 'creatorName', 'encodeLabel' => false],
                        ['attribute' => 'state', 'encodeLabel' => false],
                        'format' => 'raw'
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

    <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,

            'columns' => [
                ['attribute' => 'number', 'encodeLabel' => false, 'label' => 'Номер<br>приказа', 'value' => function (OrderTrainingWork $model) {
                    return $model->getFullNumber();
                }],
                ['attribute' => 'orderDate', 'encodeLabel' => false, 'label' => 'Дата<br>приказа', 'value' => function (OrderTrainingWork $model) {
                    return DateFormatter::format($model->order_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
                }],
                ['attribute' => 'orderName', 'encodeLabel' => false, 'label' => 'Название<br>приказа'],
                ['attribute' => 'bringName', 'label' => 'Проект<br>вносит', 'encodeLabel' => false, 'value' => function (OrderTrainingWork $model) {
                    return $model->bringWork ? $model->bringWork->peopleWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) : '---';
                }],
                ['attribute' => 'executorName', 'label' => 'Исполнитель', 'encodeLabel' => false, 'value' => function (OrderTrainingWork $model) {
                    return $model->executorWork ? $model->executorWork->peopleWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) : '---';
                }],
                ['attribute' => 'key_words', 'encodeLabel' => false, 'label' => 'Ключевые<br>слова'],

                ['class' => VerticalActionColumn::class],
            ],
            'rowOptions' => function ($model) {
                return ['data-href' => Url::to([Yii::$app->frontUrls::ORDER_TRAINING_VIEW, 'id' => $model->id])];
            },
        ]);?>

</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>