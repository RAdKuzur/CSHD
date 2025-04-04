<?php

use app\components\VerticalActionColumn;
use common\helpers\html\HtmlCreator;
use frontend\models\search\SearchAuditorium;
use frontend\models\work\dictionaries\AuditoriumWork;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel SearchAuditorium */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Помещения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auditorium-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">
                    <?php

                    $gridColumns = [
                        ['attribute' => 'name', 'label' => 'Уникальный глобальный номер'],
                        ['attribute' => 'text', 'label' => 'Имя'],
                        ['attribute' => 'square', 'label' => 'Площадь (кв.м)'],
                        ['attribute' => 'isEducation', 'label' => 'Предназначен для обр. деят.'],
                        ['attribute' => 'branch_id', 'label' => 'Название отдела', 'value' => function($model){
                            return $model->branch->name;}],
                        ['attribute' => 'capacity', 'label' => 'Кол-во ученико-мест'],
                        ['attribute' => 'auditoriumTypeString', 'label' => 'Тип помещения'],
                        ['attribute' => 'window_count', 'label' => 'Кол-во оконных проемов'],
                        ['attribute' => 'includeSquareStr', 'label' => 'Учитывается при подсчете площади'],
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

    <div style="margin-bottom: 10px">

    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [

            ['attribute' => 'name', 'label' => 'Глобальный<br>номер', 'encodeLabel' => false],
            ['attribute' => 'text', 'label' => 'Имя', 'encodeLabel' => false],
            ['attribute' => 'square', 'label' => 'Площадь<br>(кв.м.)', 'encodeLabel' => false],
            ['attribute' => 'auditoriumTypeString', 'label' => 'Тип помещения', 'encodeLabel' => false,
                'value' => function (AuditoriumWork $model) {
                    return Yii::$app->auditoriumType->get($model->auditorium_type);
                }
            ],
            ['attribute' => 'educationPretty', 'label' => 'Предназначен<br>для обр. деят.', 'encodeLabel' => false],
            ['attribute' => 'branchName', 'label' => 'Название<br>отдела', 'format' => 'html', 'encodeLabel' => false,
                'value' => function (AuditoriumWork $model) {
                    return Yii::$app->branches->get($model->branch);
                }
            ],

            ['class' => VerticalActionColumn::class],
        ],
        'rowOptions' => function ($model) {
            return ['data-href' => Url::to([Yii::$app->frontUrls::AUDITORIUM_VIEW, 'id' => $model->id])];
        },
    ]); ?>


</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>