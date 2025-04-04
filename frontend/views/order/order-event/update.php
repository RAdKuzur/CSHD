<?php

use frontend\models\work\order\OrderEventWork;
use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\DocumentOrder;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model OrderEventWork */
/* @var $people */
/* @var $foreignEventTable */
/* @var $scanFile */
/* @var $docFiles */
/* @var $foreignEventTable */
/* @var $teamTable */
/* @var $awardTable */
/* @var $nominations */
/* @var $teams */
/* @var $modelActs */
/* @var $actTable */
/* @var $participants */
/* @var $company */
/* @var $id */

$this->title = 'Изменить приказ об участии деятельности № ' . $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Приказы об участии', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $id]];
$this->params['breadcrumbs'][] = 'Редактирование';

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="order-main-update">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
            'model' => $model,
            'people' => $people,
            'scanFile' => $scanFile,
            'docFiles' => $docFiles,
            'foreignEventTable' => $foreignEventTable,
            'teamTable' => $teamTable,
            'awardTable' => $awardTable,
            'nominations' => $nominations,
            'teams' => $teams,
            'modelActs' => $modelActs,
            'actTable' => $actTable,
            'participants' => $participants,
            'company' => $company,
            'id' => $id
        ]
    ) ?>
</div>

<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= DocumentOrder::tableName() ?>', 'index.php?r=order/order-event/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>