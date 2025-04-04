<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\ForeignEventParticipants;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ForeignEventParticipantsWork */

$this->title = 'Редактировать участника деятельности: ' . $model->getFIO(ForeignEventParticipantsWork::FIO_SURNAME_INITIALS);
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getFIO(ForeignEventParticipantsWork::FIO_SURNAME_INITIALS), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="foreign-event-participants-update">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= ForeignEventParticipants::tableName() ?>', 'index.php?r=dictionaries/foreign-event-participants/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>