<?php

use app\components\VerticalActionColumn;
use common\helpers\html\HtmlCreator;
use frontend\models\search\SearchCompany;
use frontend\models\work\dictionaries\CompanyWork;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel SearchCompany */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Организации';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">
                    <?php

                    $gridColumns = [
                        ['attribute' => 'inn', 'encodeLabel' => false],
                        ['attribute' => 'name', 'encodeLabel' => false],
                        ['attribute' => 'short_name', 'encodeLabel' => false],
                        ['attribute' => 'company_type', 'label' => 'Тип организации', 'value' => function(CompanyWork $model){
                            return Yii::$app->companyType->get($model->company_type);
                        }],
                        ['attribute' => 'contractorString', 'encodeLabel' => false],
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

            ['attribute' => 'inn', 'label' => 'ИНН'],
            ['attribute' => 'name', 'label' => 'Наименование'],
            ['attribute' => 'short_name', 'label' => 'Краткое наименование'],
            ['attribute' => 'company_type', 'label' => 'Тип организации', 'value' => function(CompanyWork $model){
                return Yii::$app->companyType->get($model->company_type);
            }],
            ['attribute' => 'contractorString', 'label' => 'Является контрагентом'],

            ['class' => VerticalActionColumn::class],
        ],
        'rowOptions' => function ($model) {
            return ['data-href' => Url::to([Yii::$app->frontUrls::COMPANY_VIEW, 'id' => $model->id])];
        },
    ]); ?>

</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>