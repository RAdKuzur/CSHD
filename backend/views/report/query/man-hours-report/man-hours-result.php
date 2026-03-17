<?php

/* @var $this yii\web\View */
/* @var $manHoursResult array */
/* @var $participantsResult array */

use backend\forms\report\ManHoursReportForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php
$this->title = 'Результат отчета по обучающимся';
?>

<div>
<h2>Результат отчета по обучающимся</h2>


<p>Человеко-часы: <?= $manHoursResult['result'] ?></p>
<?php if (!empty($participantsResult)):
    $total = 0;

    if (is_array($participantsResult['result'])) {
        $total = array_sum($participantsResult['result']);
    }
    ?>
    <p>
        Обучающиеся: <strong><?= $total ?></strong>
    </p>
    <table class="table table-striped">
        <?php if (is_array($participantsResult['result'])): ?>
            <?php foreach($participantsResult['result'] as $index => $participantChapter): ?>
                <tr><td><?= ManHoursReportForm::$types[$index] ?></td><td><?= $participantChapter ?></td></tr>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?= $participantsResult['result'] ?></p>
        <?php endif; ?>
    </table>
<?php else: ?>
<!--    <p>вывод обучающихся доступен только при выборе одного преподавателя:</p>-->
<?php endif; ?>

<?php if (!empty($manHoursResult) || !empty($participantsResult)): ?>
    <h2>Опция скачивания:</h2>
<?php endif; ?>

<?php if (!empty($manHoursResult)): ?>
    <?php $form1 = ActiveForm::begin(['method' => 'post', 'action' => ['download-debug-csv', 'type' => ManHoursReportForm::MAN_HOURS_REPORT]]); ?>
        <input type="hidden" name="debugData" value="<?= htmlspecialchars(json_encode($manHoursResult['debugData'], JSON_UNESCAPED_UNICODE)) ?>">
        <?= Html::submitButton('Скачать подробный отчет по человеко-часам',  ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
<?php endif; ?>

<?php if (!empty($participantsResult)): ?>
    <?php $form2 = ActiveForm::begin(['method' => 'post', 'action' => ['download-debug-csv', 'type' => ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN]]); ?>
        <input type="hidden" name="debugData" value="<?= htmlspecialchars(json_encode($participantsResult['debugData'], JSON_UNESCAPED_UNICODE)) ?>">
        <?= Html::submitButton('Скачать подробный отчет по обучающимся', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
<?php endif; ?>

</div>

