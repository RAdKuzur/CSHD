<?php

use backend\models\search\SearchUser;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel SearchUser */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = ['label' => 'Список пользователей', 'url' => ['dictionaries/users']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить пользователя', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Выдать токен', ['tokens'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'surname', 'label' => 'Фамилия'],
            ['attribute' => 'firstname', 'label' => 'Имя'],
            ['attribute' => 'patronymic', 'label' => 'Отчество'],
            ['attribute' => 'username', 'label' => 'Логин'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
