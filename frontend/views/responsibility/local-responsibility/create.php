<?php

use frontend\forms\ResponsibilityForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ResponsibilityForm */
/* @var $audsList */
/* @var $peoples */
/* @var $orders */
/* @var $regulations */

$this->title = 'Добавление новой ответственности работника';
$this->params['breadcrumbs'][] = ['label' => 'Учет ответственности работников', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="local-responsibility-create">

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'audsList' => $audsList,
        'peoples' => $peoples,
        'orders' => $orders,
        'regulations' => $regulations,
    ]) ?>

</div>
