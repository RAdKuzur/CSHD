<?php

use common\components\wizards\AlertMessageWizard;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\DocumentOrder;
use frontend\models\work\order\DocumentOrderWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\order\OrderTrainingWork */
/* @var $people */
/* @var $groups */
/* @var $groupParticipant */
/* @var $transferGroups */
/* @var $scanFile */
/* @var $docFiles */
/* @var $groupCheckOption */
/* @var $groupParticipantOption */
/* @var $error */
$this->title = 'Изменить приказ об образовательной деятельности № '. $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Приказы об образовательной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="order-training-update">
    <?php
        switch ($error) {
            case DocumentOrderWork::ERROR_DATE_PARTICIPANT:
                echo HtmlBuilder::createMessage(HtmlBuilder::TYPE_WARNING, 'Ошибка выбора даты приказа','Невозможно применить действие к ученикам.');
                break;
            case DocumentOrderWork::ERROR_RELATION:
                echo HtmlBuilder::createMessage(HtmlBuilder::TYPE_WARNING, 'Выбранные обучающиеся задействованы в других приказах','Невозможно применить действие к ученикам.');
                break;
        }
    ?>
    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people,
        'groups' => $groups,
        'groupParticipant' => $groupParticipant,
        'transferGroups' => $transferGroups,
        'scanFile' => $scanFile,
        'docFiles' => $docFiles,
        'groupCheckOption' => $groupCheckOption,
        'groupParticipantOption' => $groupParticipantOption,
    ]) ?>
</div>

<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= DocumentOrder::tableName() ?>', 'index.php?r=order/order-training/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>