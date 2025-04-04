<?php

use frontend\models\work\educational\training_program\TrainingProgramWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrainingProgramWork */
/* @var $ourPeople */
/* @var $modelAuthor array */
/* @var $modelThematicPlan array */

$this->title = 'Добавить образовательную программу';
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-program-create">

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'ourPeople' => $ourPeople,
        'modelAuthor' => $modelAuthor,
        'modelThematicPlan' => $modelThematicPlan,
    ]) ?>

</div>
