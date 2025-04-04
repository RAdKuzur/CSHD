<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\DocumentOut;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\document_in_out\DocumentOutWork */
/* @var $correspondentList */
/* @var $availablePositions */
/* @var $availableCompanies */
/* @var $mainCompanyWorkers */
/* @var $scanFile */
/* @var $docFiles */
/* @var $appFiles */
/* @var $filesAnswer */
$this->title = 'Исходящий документ №' . $model->fullNumber;
$this->params['breadcrumbs'][] = ['label' => 'Исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
    <div class="document-out-create">

        <?= AlertMessageWizard::showRedisConnectMessage() ?>

        <div class="substrate">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>

        <?= $this->render('_form', [
            'model' => $model,
            'correspondentList' => $correspondentList,
            'availablePositions' => $availablePositions,
            'availableCompanies' => $availableCompanies,
            'mainCompanyWorkers' => $mainCompanyWorkers,
            'scanFile' => $scanFile,
            'docFiles' => $docFiles,
            'appFiles' => $appFiles,
            'filesAnswer' => $filesAnswer,

        ]) ?>

    </div>

<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= DocumentOut::tableName() ?>', 'index.php?r=document/document-out/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>
