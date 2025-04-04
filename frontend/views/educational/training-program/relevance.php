<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\SearchTrainingProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Изменить актуальность образовательных программ';
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => Url::toRoute([Yii::$app->frontUrls::PROGRAM_INDEX])];
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

    <?= $this->render('_search-relevance', ['searchModel' => $searchModel]) ?>

    <div style="margin-bottom: 10px">

    <?= GridView::widget([
        'id'=>'grid',
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn', 'header' => 'Программа<br>актуальна',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    $options['checked'] = (bool)$model->actual;
                    $options['class'] = 'check';
                    return $options;
                }],

            ['attribute' => 'namePretty', 'format' => 'raw'],
            ['attribute' => 'levelNumber', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'branchString', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'pedCouncilDate', 'encodeLabel' => false, 'label' => 'Дата<br>пед. сов.'],
            ['attribute' => 'authorString', 'format' => 'html'],
            ['attribute' => 'capacity'],
            ['attribute' => 'focusString'],
            ['attribute' => 'fullDirectionName', 'encodeLabel' => false, 'label' => 'Тематическое<br>направление'],
        ],
    ]); ?>

</div>

    <?php
    return Yii::$app->view->renderFile(Yii::$app->frontUrls::ACTUAL_OBJECT, [
        'idAttribute' => '#relevance-save',
        'urlString' => 'relevance-save',
        'urlBackString' => 'index',
    ]);
    ?>


