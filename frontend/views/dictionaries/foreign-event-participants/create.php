<?php

use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ForeignEventParticipantsWork */

$this->title = 'Добавление нового участника деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-participants-create">

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
