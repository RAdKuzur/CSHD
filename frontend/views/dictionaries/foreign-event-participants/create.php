<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\ForeignEventParticipantsWork */

$this->title = 'Добавление нового участника деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-participants-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
