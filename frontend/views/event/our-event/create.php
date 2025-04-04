<?php

use frontend\models\work\event\EventWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model EventWork */
/* @var $people PeopleWork */
/* @var $regulations RegulationWork */
/* @var $branches array */

$this->title = 'Добавить мероприятие';
$this->params['breadcrumbs'][] = ['label' => 'Мероприятия', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-create">

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people,
        'regulations' => $regulations,
        'branches' => $branches,
    ]) ?>

</div>
