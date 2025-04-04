<?php

use common\helpers\DateFormatter;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\SearchTrainingProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Изменить архивность учебных групп';
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => Url::toRoute([Yii::$app->frontUrls::TRAINING_GROUP_INDEX])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="training-program-index">
    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
            </div>
        </div>
    </div>

    <?= $this->render('_search-archive', ['searchModel' => $searchModel]) ?>

    <div style="margin-bottom: 10px">

    <?= GridView::widget([
        'id'=>'grid',
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn', 'header' => 'Группа<br>архивна',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    $options['checked'] = (bool)$model->archive;
                    $options['class'] = 'check';
                    return $options;
                }],

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
        ],
    ]); ?>

</div>

    <?= $this->render(Yii::$app->frontUrls::ACTUAL_OBJECT, [
        'idAttribute' => '#archive-save',
        'urlString' => 'archive-save',
        'urlBackString' => 'index',
    ]);
    ?>


