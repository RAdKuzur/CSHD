<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\ForeignEvent;
use frontend\forms\event\ForeignEventForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ForeignEventForm */
/* @var $peoples */
/* @var $orders6 */
/* @var $orders9 */
/* @var $modelAchievements */

$this->title = 'Редактировать: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Учет достижений в мероприятиях', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="foreign-event-update">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'peoples' => $peoples,
        'orders6' => $orders6,
        'orders9' => $orders9,
        'modelAchievements' => $modelAchievements
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