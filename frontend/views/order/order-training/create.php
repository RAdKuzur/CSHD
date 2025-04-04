<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\order\OrderTrainingWork */
/* @var $model */
/* @var $people */
/* @var $groups */
/* @var $groupParticipant */
/* @var $groupCheckOption */
/* @var $groupParticipantOption */
$this->title = 'Добавить приказ об образовательной деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Приказы об образовательной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-training-create">

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people,
        'groups' => $groups,
        'groupParticipant' => $groupParticipant,
        'groupCheckOption' => $groupCheckOption,
        'groupParticipantOption' => $groupParticipantOption,
    ]) ?>

</div>


