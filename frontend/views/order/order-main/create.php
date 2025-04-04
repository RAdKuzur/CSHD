<?php

use frontend\models\work\order\OrderMainWork;
use yii\helpers\Html;

/* @var $model OrderMainWork */
/* @var $people */
/* @var $modelExpire */
/* @var $orders */
/* @var $regulations */
$this->title = 'Добавить приказ об основной деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Приказы об осн. деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="">

        <div class="substrate">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>

        <?= $this->render('_form', [
            'model' => $model,
            'people' => $people,
            'modelExpire' => $modelExpire,
            'orders' => $orders,
            'regulations' => $regulations
        ]) ?>

    </div>

