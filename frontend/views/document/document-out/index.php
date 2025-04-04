<?php

use app\components\VerticalActionColumn;
use common\helpers\DateFormatter;
use common\helpers\html\HtmlCreator;
use common\helpers\StringFormatter;
use frontend\models\work\document_in_out\DocumentOutWork;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \frontend\models\search\SearchDocumentOut */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\models\work\document_in_out\DocumentOutWork */
/* @var $peopleList */
/* @var $buttonsAct */

$this->title = 'Исходящая документация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="document-out-index">
    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">
                    <?php

                        $gridColumns = [
                            ['attribute' => 'fullNumber'],
                            ['attribute' => 'documentDate', 'encodeLabel' => false],
                            ['attribute' => 'sentDate', 'encodeLabel' => false],
                            ['attribute' => 'documentNumber', 'encodeLabel' => false],

                            ['attribute' => 'companyName', 'encodeLabel' => false],
                            ['attribute' => 'documentTheme', 'encodeLabel' => false],
                            ['attribute' => 'sendMethodName',
                                'value' => function(DocumentOutWork $model) {
                                    return Yii::$app->sendMethods->get($model->send_method);
                                }
                            ],
                            ['attribute' => 'isAnswer',
                                'value' => function(DocumentOutWork $model) {
                                    return $model->getIsAnswer();
                                },
                                'format' => 'raw'
                            ],
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

    <div style="margin-bottom: 20px">

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'columns' => [
                ['attribute' => 'fullNumber'],
                ['attribute' => 'documentDate',
                    'value' => function(DocumentOutWork $model) {
                        return DateFormatter::format($model->document_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
                    },
                    'encodeLabel' => false,
                    'format' => 'raw',
                ],
                ['attribute' => 'documentTheme'],
                ['attribute' => 'companyName',
                    'encodeLabel' => false,
                    'format' => 'raw',
                ],

                ['attribute' => 'executorName'],
                ['attribute' => 'sendMethodName',
                    'value' => function(DocumentOutWork $model) {
                        return Yii::$app->sendMethods->get($model->send_method);
                    }
                ],
                ['attribute' => 'sentDate',
                    'value' => function(DocumentOutWork $model) {
                        return DateFormatter::format($model->sent_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
                    },
                    'encodeLabel' => false,
                    'format' => 'raw',
                ],
                ['attribute' => 'isAnswer',
                    'value' => function(DocumentOutWork $model) {
                        return $model->getIsAnswer(StringFormatter::FORMAT_LINK);
                    },
                    'encodeLabel' => false,
                    'format' => 'raw',
                ],

                ['class' => VerticalActionColumn::class],
            ],
            'rowOptions' => function ($model) {
                return ['data-href' => Url::to([Yii::$app->frontUrls::DOC_OUT_VIEW, 'id' => $model->id])];
            },
        ]);

        ?>
    </div>

    <?= $this->render('modal-reserve', [
        'model' => $model,
        'peopleList' => $peopleList,
        'dataProvider' => $dataProvider,
    ]);
    ?>

</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>


