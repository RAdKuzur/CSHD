<?php

use frontend\models\work\dictionaries\AuditoriumWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model AuditoriumWork */

$this->title = 'Добавить помещение';
$this->params['breadcrumbs'][] = ['label' => 'Помещения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auditorium-create">

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
