<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\ForeignEvent;
use frontend\models\work\event\EventWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model EventWork */
/* @var $people PeopleWork */
/* @var $regulations RegulationWork[] */
/* @var $branches array */
/* @var $groups array */
/* @var $protocolFiles */
/* @var $photoFiles */
/* @var $reportingFiles */
/* @var $otherFiles */
/* @var $modelGroups array */
/* @var $orders DocumentOrderWork[] */

$this->title = 'Редактировать мероприятие: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Мероприятия', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<style>
    .lift-button {
        position: fixed;
        width: 60px;
        height: 60px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        transition: opacity 0.3s;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        z-index: 1000;
    }

    .lift-button:hover {
        background: #0056b3;
    }
</style>

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
        'groups' => $groups,
        'protocolFiles' => $protocolFiles,
        'photoFiles' => $photoFiles,
        'reportingFiles' => $reportingFiles,
        'otherFiles' => $otherFiles,
        'modelGroups' => $modelGroups,
        'orders' => $orders
    ]) ?>

    <button id="scrollToTop" title="Наверх" class="lift-button" style="bottom: 170px;
        right: 100px;" >↑</button>

    <button id="scrollToBottom" title="Наверх" class="lift-button" style="bottom: 100px;
        right: 100px;">↓</button>
</div>



<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= ForeignEvent::tableName() ?>', 'index.php?r=event/foreign-event/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);

    const scrollToTopButton = document.getElementById('scrollToTop');
    scrollToTopButton.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    const scrollToBottomButton = document.getElementById('scrollToBottom');
    scrollToBottomButton.addEventListener('click', () => {
        window.scrollTo({
            top: document.body.scrollHeight,
            behavior: 'smooth'
        });
    });
</script>
