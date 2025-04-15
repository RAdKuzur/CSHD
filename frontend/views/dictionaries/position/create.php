<?php

use frontend\models\work\dictionaries\PositionWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PositionWork */

$this->title = 'Добавить должность';
$this->params['breadcrumbs'][] = ['label' => 'Должности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="position-create">

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
