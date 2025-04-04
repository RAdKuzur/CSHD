<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\ForeignEvent;
use frontend\models\work\event\EventWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model EventWork */
/* @var $people PeopleWork */
/* @var $regulations RegulationWork */
/* @var $branches array */
/* @var $protocolFiles */
/* @var $photoFiles */
/* @var $reportingFiles */
/* @var $otherFiles */

$this->title = 'Редактировать мероприятие: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Мероприятия', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="event-update">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people,
        'regulations' => $regulations,
        'branches' => $branches,
        'protocolFiles' => $protocolFiles,
        'photoFiles' => $photoFiles,
        'reportingFiles' => $reportingFiles,
        'otherFiles' => $otherFiles,
    ]) ?>

</div>

<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= ForeignEvent::tableName() ?>', 'index.php?r=event/foreign-event/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>
