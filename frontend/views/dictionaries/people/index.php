<?php

use app\components\VerticalActionColumn;
use common\helpers\html\HtmlCreator;
use frontend\models\search\SearchPeople;
use frontend\models\work\general\PeopleWork;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel SearchPeople */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Люди';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="people-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">
                    <?php

                    $gridColumns = [
                        ['attribute' => 'surname', 'encodeLabel' => false],
                        ['attribute' => 'firstname', 'encodeLabel' => false],
                        ['attribute' => 'patronymic', 'encodeLabel' => false],
                        ['attribute' => 'positionsWork', 'encodeLabel' => false],
                        ['attribute' => 'companyName', 'encodeLabel' => false],
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

    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [

            ['attribute' => 'surname', 'label' => 'Фамилия'],
            ['attribute' => 'firstname', 'label' => 'Имя'],
            ['attribute' => 'patronymic', 'label' => 'Отчество'],
            ['attribute' => 'positionsWork', 'label' => 'Должности', 'format' => 'raw'],
            /*['attribute' => 'positionsWork', 'label' => 'Должность', 'format' => 'raw'],*/ // вот это работает, но нужно подшаманить с фильтрами
            ['attribute' => 'companyName', 'label' => 'Организация', 'value' => function(PeopleWork $model){
                return $model->company->name;
            }],

            ['class' => VerticalActionColumn::class],
        ],
        'rowOptions' => function ($model) {
            return ['data-href' => Url::to([Yii::$app->frontUrls::PEOPLE_VIEW, 'id' => $model->id])];
        },
    ]); ?>

</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>
