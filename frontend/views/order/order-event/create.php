<?php

use frontend\models\work\order\OrderEventWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model OrderEventWork */
/* @var $people */
/* @var $modelActs */
/* @var $nominations */
/* @var $teams */
/* @var $participants */
/* @var $company */
$this->title = 'Добавить приказ об участии';
$this->params['breadcrumbs'][] = ['label' => 'Приказы об участии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="">

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people,
        'modelActs' => $modelActs,
        'nominations' => $nominations,
        'teams' => $teams,
        'participants' => $participants,
        'company' => $company,
    ]) ?>

</div>


