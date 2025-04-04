<?php

use app\components\VerticalActionColumn;
use common\helpers\html\HtmlCreator;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\responsibility\LocalResponsibilityWork;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchLocalResponsibility */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Учет ответственности работников';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="local-responsibility-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">
                    <?php

                    $gridColumns = [
                        ['attribute' => 'responsibilityTypeStr', 'format' => 'raw', 'label' => 'Вид ответственности',
                            'value' => function (LocalResponsibilityWork $responsibility) {
                                return Yii::$app->responsibilityType->get($responsibility->responsibility_type);
                            }
                        ],
                        ['attribute' => 'branchStr', 'format' => 'raw', 'label' => 'Отдел',
                            'value' => function (LocalResponsibilityWork $responsibility) {
                                return Yii::$app->branches->get($responsibility->branch);
                            }
                        ],
                        ['attribute' => 'auditoriumStr', 'format' => 'raw', 'label' => 'Помещение',
                            'value' => function (LocalResponsibilityWork $responsibility) {
                                return $responsibility->auditoriumWork->name;
                            }
                        ],
                        ['attribute' => 'quant', 'format' => 'raw', 'label' => 'Квант'],
                        ['attribute' => 'peopleStr', 'format' => 'raw', 'label' => 'Работник',
                            'value' => function (LocalResponsibilityWork $responsibility) {
                                return $responsibility->peopleStampWork->peopleWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS);
                            }
                        ],
                        ['attribute' => 'regulationStr', 'format' => 'raw', 'label' => 'Положение/инструкция',
                            'value' => function (LocalResponsibilityWork $responsibility) {
                                return $responsibility->regulationWork->name;
                            }
                        ]
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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
        'columns' => [

            ['attribute' => 'responsibilityTypeStr', 'format' => 'raw', 'label' => 'Вид ответственности',
                'value' => function (LocalResponsibilityWork $responsibility) {
                    return Yii::$app->responsibilityType->get($responsibility->responsibility_type);
                }
            ],
            ['attribute' => 'branchStr', 'format' => 'raw', 'label' => 'Отдел',
                'value' => function (LocalResponsibilityWork $responsibility) {
                    return Yii::$app->branches->get($responsibility->branch);
                }
            ],
            ['attribute' => 'auditoriumStr', 'format' => 'raw', 'label' => 'Помещение',
                'value' => function (LocalResponsibilityWork $responsibility) {
                    return $responsibility->auditoriumWork->name;
                }
            ],
            ['attribute' => 'quant', 'format' => 'raw', 'label' => 'Квант'],
            ['attribute' => 'peopleStr', 'format' => 'raw', 'label' => 'Работник',
                'value' => function (LocalResponsibilityWork $responsibility) {
                    return $responsibility->peopleStampWork->peopleWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS);
                }
            ],
            ['attribute' => 'regulationStr', 'format' => 'raw', 'label' => 'Положение/инструкция',
                'value' => function (LocalResponsibilityWork $responsibility) {
                    return $responsibility->regulationWork->name;
                }
            ],

            ['class' => VerticalActionColumn::class],
        ],
        'rowOptions' => function ($model) {
            return ['data-href' => Url::to([Yii::$app->frontUrls::LOCAL_RESPONSIBILITY_VIEW, 'id' => $model->id])];
        },
    ]); ?>


</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>