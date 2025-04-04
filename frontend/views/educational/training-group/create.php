<?php

use frontend\forms\training_group\TrainingGroupBaseForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrainingGroupBaseForm */
/* @var $modelTeachers */
/* @var $trainingPrograms */
/* @var $people */

$this->title = 'Добавить учебную группу';
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-base', [
        'model' => $model,
        'modelTeachers' => $modelTeachers,
        'trainingPrograms' => $trainingPrograms,
        'people' => $people
    ]) ?>

</div>