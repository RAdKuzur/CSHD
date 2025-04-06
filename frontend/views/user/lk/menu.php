<?php

/* @var $this yii\web\View */
/* @var $model \common\models\work\UserWork */

use common\models\work\UserWork;
use yii\helpers\Url;

$this->title = $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['info', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<style>
    .category-wrap {
        padding: 5px;
        background: white;
        width: 200px;
        box-shadow: 2px 2px 8px rgba(0,0,0,.1);
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
    .category-wrap h3 {
        font-size: 16px;
        color: rgba(0,0,0,.6);
        margin: 0 0 10px;
        padding: 0 5px;
        position: relative;
    }
    .category-wrap h3:after {
        content: "";
        width: 6px;
        height: 6px;
        background: #ADD8E6;
        position: absolute;
        right: 5px;
        bottom: 2px;
        box-shadow: -8px -8px #ADD8E6, 0 -8px #ADD8E6, -8px 0 #ADD8E6;
    }
    .category-wrap ul {
        list-style: none;
        margin: 0;
        padding: 0;
        border-top: 1px solid rgba(0,0,0,.3);
    }
    .category-wrap li {margin: 12px 0 0 0px; list-style-type: none;}
    .category-wrap a {
        text-decoration: none;
        display: flex;
        font-size: 15px;
        color: black;
        padding: 5px;
        position: relative;
        transition: .3s linear;
    }
    .category-wrap a:after {
        font-family: FontAwesome;
        position: absolute;
        right: 5px;
        color: white;
        transition: .2s linear;
    }
    .category-wrap a:hover {
        background: #ADD8E6;
        color: black;
    }
</style>

<script>

</script>

<div class="local-responsibility-view" style="float: left; padding-right: 20px">
    <div class="widget">
        <ul class="category-wrap">

            <!-- Тут как будто бы нужно уже предикатами баловаться. Но позже -->
            <?php $curIndex = Yii::$app->session->get('lk-index')?>
            <li <?= 'style="background-color: #ADD8E6"' ?>><a href="<?= Url::to(['/user/lk/info', 'id' => $model->id]) ?>">Профиль</a></li>
            <li <?= 'style="background-color: #ADD8E6"' ?>><a href="<?= Url::to(['/user/lk/change-password', 'id' => $model->id]) ?>">Изменить пароль</a></li>
        </ul>
    </div>
</div>