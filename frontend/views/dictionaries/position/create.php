<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\PositionWork */

$this->title = 'Добавить должность';
$this->params['breadcrumbs'][] = ['label' => 'Должности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="position-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
