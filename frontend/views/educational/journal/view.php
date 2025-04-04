<?php

use common\helpers\DateFormatter;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use frontend\forms\journal\JournalForm;
use frontend\forms\journal\ThematicPlanForm;
use frontend\models\work\dictionaries\PersonInterface;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model JournalForm */
/* @var $plan ThematicPlanForm */
/* @var $buttonsAct */
/* @var $otherButtonsAct */

$this->title = 'Электронный журнал';
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_INDEX]];
$this->params['breadcrumbs'][] = ['label' => 'Группа ' . $model->getTrainingGroupNumber(), 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $model->groupId]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="journal-view">
    <div class="substrate">
        <div class="flexx">
            <h1>
                <?= Html::encode('Журнал ' . $model->getTrainingGroupNumber()) ?>
            </h1>
            <h3>
                <?= $model->getRawArchiveGroup(); ?>
            </h3>
        </div>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
            </div>
            <div class="flexx">
                <?= $otherButtonsAct; ?>
            </div>
        </div>
    </div>

    <div class="card no-flex">
        <div class="table-topic">
            <?= $this->title ?>
        </div>
        <div class="table-block scroll">
            <table>
                <thead>
                    <tr>
                        <th>ФИО</th>
                        <th colspan="<?= $model->getLessonsCount() ?>">Расписание</th>
                        <th colspan="<?= $model->getColspanControl() ?>">Итоговый контроль</th>
                    </tr>
                <tr>
                    <td>учащегося</td>
                    <?php foreach ($model->getDateLessons() as $dateLesson): ?>
                        <td class="lessons-date"> <?= $dateLesson ?>  </td>
                    <?php endforeach; ?>
                    <td style="display: <?= $model->isProjectCertificate() ? 'block' : 'none';?>">Тема проекта</td>
                    <td style="display: <?= $model->isControlWorkCertificate() ? 'block' : 'none';?>">Оценка</td>
                    <td>Успешное завершение</td>
                </tr>
                </thead>

                <tbody>
                    <?php foreach ($model->participantLessons as $participantLesson): ?>
                        <tr>
                            <td>
                                <div class="flexx space">
                                    <?= $model->getParticipantIcons($participantLesson->participant); ?>
                                    <?= $model->getPrettyParticipant($participantLesson->participant, StringFormatter::FORMAT_LINK); ?>
                                </div>
                            </td>
                            <?php foreach ($participantLesson->lessonIds as $lesson): ?>
                                <td class="status-participant">
                                    <?= $lesson->getPrettyStatus() ?>
                                </td>
                            <?php endforeach; ?>
                            <td style="display: <?= $model->isProjectCertificate() ? 'block' : 'none';?>">
                                <?= $participantLesson->groupProjectThemesWork->projectThemeWork->name; ?>
                            </td>
                            <td class="status-participant" style="display: <?= $model->isControlWorkCertificate() ? 'block' : 'none';?>">
                                <?= $participantLesson->points; ?>
                            </td>
                            <td class="status-participant">
                                <?= $model->getPrettySuccessFinishing($participantLesson->successFinishing); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card no-flex">
        <div class="table-topic">
            Тематический план (ТП)
        </div>
        <div class="table-block">
            <table>
                <thead>
                    <tr>
                        <th>Дата занятия</th>
                        <th>Тема занятия</th>
                        <th>Форма контроля</th>
                        <th>Педагоги</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($plan->lessonThemes as $index => $lessonTheme): ?>
                    <tr>
                        <td>
                            <?=
                            DateFormatter::format(
                                $lessonTheme->trainingGroupLessonWork->lesson_date,
                                DateFormatter::Ymd_dash,
                                DateFormatter::dmY_dot
                            )
                            ?>
                        </td>
                        <td>
                            <?= $lessonTheme->thematicPlanWork->theme ?>
                        </td>
                        <td>
                            <?= Yii::$app->controlType->get($lessonTheme->thematicPlanWork->control_type) ?>
                        </td>
                        <td>
                            <?= $lessonTheme->teacherWork->peopleWork->getFIO(PersonInterface::FIO_FULL) ?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
    <?= HtmlBuilder::upButton();?>
</div>
