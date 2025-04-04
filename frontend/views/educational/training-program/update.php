<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\TrainingProgram;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrainingProgramWork */
/* @var $ourPeople */
/* @var $modelAuthor array */
/* @var $modelThematicPlan array */
/* @var $mainFile */
/* @var $docFiles */
/* @var $contractFile */

$this->title = 'Редактировать образовательную программу: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="training-program-update">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'ourPeople' => $ourPeople,
        'modelAuthor' => $modelAuthor,
        'modelThematicPlan' => $modelThematicPlan,
        'mainFile' => $mainFile,
        'docFiles' => $docFiles,
        'contractFile' => $contractFile,
    ]) ?>

</div>

<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= TrainingProgram::tableName() ?>', 'index.php?r=educational/training-program/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>