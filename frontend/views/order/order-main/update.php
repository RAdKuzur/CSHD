<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\DocumentOrder;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\order\OrderMainWork */
/* @var $people */
/* @var $modelExpire */
/* @var $orders */
/* @var $regulations */
/* @var $modelChangedDocuments */
/* @var $scanFile */
/* @var $docFiles */

$this->title = 'Приказ об основной деятельности №' . $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Приказы об основной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
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
        'modelExpire' => $modelExpire,
        'orders' => $orders,
        'regulations' => $regulations,
        'modelChangedDocuments' => $modelChangedDocuments,
        'scanFile' => $scanFile,
        'docFiles' => $docFiles,
    ]) ?>
</div>
<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= DocumentOrder::tableName() ?>', 'index.php?r=order/order-main/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>