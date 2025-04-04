<?php

use common\helpers\files\FilePaths;
use common\helpers\html\HtmlBuilder;
use frontend\forms\journal\JournalForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model JournalForm */
/* @var $buttonsAct */
/* @var $permissionsLessons */

$this->title = 'Редактирование журнала ' . $model->getTrainingGroupNumber();
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_INDEX]];
$this->params['breadcrumbs'][] = ['label' => 'Группа ' . $model->getTrainingGroupNumber(), 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $model->groupId]];
$this->params['breadcrumbs'][] = ['label' => 'Электронный журнал', 'url' => [Yii::$app->frontUrls::JOURNAL_VIEW, 'id' => $model->groupId]];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/journal.js', ['position' => $this::POS_HEAD]);
?>

<script>
    /**
     * Инициализация иконок
     */
    function init() {
        IconTurnoutLink = '<?= Url::base(true) .'/'. FilePaths::SVG_TURNOUT ?>';
        IconNonAppearanceLink = '<?= Url::base(true) .'/'. FilePaths::SVG_NON_APPEARANCE ?>';
        IconDistantLink = '<?= Url::base(true) .'/'. FilePaths::SVG_DISTANT ?>';
        IconDroppedLink = '<?= Url::base(true) .'/'. FilePaths::SVG_DROPPED ?>';
        IconProjectLink = '<?= Url::base(true) .'/'. FilePaths::SVG_PROJECT ?>';
        elements = document.getElementsByTagName('input');
        elementsProject = document.getElementsByTagName('select');

        saveSvgFile(IconTurnoutLink, IconNonAppearanceLink, IconDistantLink, IconDroppedLink, IconProjectLink);
        let cell = document.getElementsByClassName('attendance');
        Array.from(cell).forEach(oneCell => {
            if (!oneCell.classList.contains('status-block')) {
                const handler = eventHandler(oneCell);
                oneCell.addEventListener('click', eventHandler(oneCell));
                oneCell._handler = handler;
            }
        });
    }
</script>

<div class="journal-edit">

    <?php $form = ActiveForm::begin(); ?>

    <div class="substrate">
        <div class="flexx">
            <h1>
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
        <div class="flexx space">
            <div class="flexx">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <div class="control-unit">
        <div class="control-label">Выберите статус одной из кнопок расположенных ниже и нажмите на ячейки или столбцы, в которые необходимо установить значение</div>
        <div class="icons-container flexx space-around">
            <div class="icon-button flexx btn-secondary btn" onclick="changeCursorAndSaveIcon(IconTurnoutLink, event)">Явка<?= HtmlBuilder::paintSVG(FilePaths::SVG_TURNOUT)?></div>
            <div class="icon-button flexx btn-secondary btn" onclick="changeCursorAndSaveIcon(IconNonAppearanceLink, event)">Неявка<?= HtmlBuilder::paintSVG(FilePaths::SVG_NON_APPEARANCE)?></div>
            <div class="icon-button flexx btn-secondary btn" onclick="changeCursorAndSaveIcon(IconDistantLink, event)">Дистант<?= HtmlBuilder::paintSVG(FilePaths::SVG_DISTANT)?></div>
            <div class="icon-button flexx btn-secondary btn" onclick="changeCursorAndSaveIcon(IconDroppedLink, event)">Нет данных<?= HtmlBuilder::paintSVG(FilePaths::SVG_DROPPED)?></div>
        </div>
        <div class="icons-container flexx space-around" style="display: <?= $model->isProjectCertificate() ? 'flex' : 'none';?>">
            <?php
            foreach($model->getProjectThemeName() as $theme) {
                if ($theme != '') {
                    echo '<div class="button-icon flexx btn-secondary btn" onclick="changeCursorAndSaveIcon(IconProjectLink, event)" data-value="'.$theme['value'].'">' . $theme['name'] .'</div>';
                }
            }
            ?>
        </div>
    </div>

    <div class="journal-form">

        <?= $form->field($model, 'groupId')->hiddenInput()->label(false) ?>
        <div class="card no-flex">
            <div class="table-topic flexx space">
                <div class="m-auto">
                    Электронный журнал
                </div>
                <div class="flexx">
                    <a class="btn btn-success btn-resize" onclick="resize(100)">+</a>
                    <a class="btn btn-warning btn-resize" onclick="resize(-100)">-</a>
                </div>
            </div>
            <div class="table-block scroll" id="journal">
                <table>
                    <thead id="journal-thead">
                    <tr>
                        <th>ФИО</th>
                        <th colspan="<?= $model->getLessonsCount() ?>">Расписание</th>
                        <th colspan="<?= $model->getColspanControl() ?>">Итоговый контроль</th>
                    </tr>
                    <tr class="sticky-cell">
                        <td>учащегося</td>
                        <?php foreach ($model->getDateLessons() as $key => $dateLesson) {
                            echo '<td class="lessons-date" onclick="clickOneCellThead(this, '.($key+1).')"> '.$dateLesson.'</td>';
                        }
                        ?>
                        <td style="display: <?= $model->isProjectCertificate() ? 'block' : 'none';?>" onclick="clickOneCellThead(this, <?= ($model->getLessonsCount() + 1)?>)">Тема проекта</td>
                        <td style="display: <?= $model->isControlWorkCertificate() ? 'block' : 'none';?>">Оценка</td>
                        <td>Успешное завершение</td>
                    </tr>
                    </thead>

                    <tbody id="journal-tbody">
                    <?php foreach ($model->participantLessons as $participantLesson): ?>
                        <tr>
                            <td class="sticky-cell">
                                <div class="flexx space-around">
                                    <?= $model->getParticipantIcons($participantLesson->participant); ?>
                                    <?= $model->getPrettyParticipant($participantLesson->participant); ?>
                                </div>
                            </td>
                            <?php foreach ($participantLesson->lessonIds as $index => $lesson): ?>
                                <td class="status-participant attendance <?= array_search($lesson->lessonId, $permissionsLessons) ? '' : 'status-block' ?>">
                                    <?= $form->field($lesson, "[$participantLesson->trainingGroupParticipantId][$index]lessonId")
                                        ->hiddenInput(['value' => $lesson->lessonId])
                                        ->label(false) ?>

                                    <?= $form->field($lesson, "[$participantLesson->trainingGroupParticipantId][$index]status")
                                        ->hiddenInput([
                                            'readonly' => true,
                                            'class' => 'status'
                                        ])
                                        ->label(false); ?>

                                    <?= $lesson->getPrettyStatus() ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="project-participant attendance" style="display: <?= $model->isProjectCertificate() ? 'block' : 'none';?>">
                                <?= $form->field($participantLesson, "[$participantLesson->trainingGroupParticipantId]groupProjectThemeId")->dropDownList(
                                    ArrayHelper::map(
                                            array_filter($model->availableThemes, function ($theme) {
                                                return $theme['confirm'] === 1;
                                            }),
                                            'id',
                                            'projectThemeWork.name'),
                                    ['prompt' => '']
                                )->label(false) ?>
                            </td>
                            <td class="status-participant" style="display: <?= $model->isControlWorkCertificate() ? 'flex' : 'none';?>">
                                <?= $form->field($participantLesson, "[$participantLesson->trainingGroupParticipantId]points")->textInput(['type' => 'number'])->label(false) ?>
                            </td>
                            <td class="status-participant success-checkbox">
                                <?= $form->field($participantLesson, "[$participantLesson->trainingGroupParticipantId]successFinishing")->checkbox()->label(false) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php ActiveForm::end(); ?>
    <?= HtmlBuilder::upButton();?>
</div>