<?php

use frontend\models\work\dictionaries\AuditoriumWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model AuditoriumWork */
/* @var $otherFiles */

$this->title = 'Редактировать помещение: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Помещения', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="auditorium-update">

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'otherFiles' => $otherFiles,
    ]) ?>

</div>
