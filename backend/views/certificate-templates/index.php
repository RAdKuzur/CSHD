<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Шаблоны сертификатов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificat-templates-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить новую подложку для шаблона сертификата', ['create-template'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'header' => '№ п/п'],
            ['attribute' => 'name', 'label' => 'Наименование'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>