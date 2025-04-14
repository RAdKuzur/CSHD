<?php

use common\helpers\ButtonsFormatter;
use common\helpers\html\HtmlBuilder;
use frontend\forms\training_group\TrainingGroupCombinedForm;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\rubac\PermissionFunctionWork;
use frontend\services\educational\JournalService;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrainingGroupCombinedForm */
/* @var $journalState */
/* @var $buttonsAct */

$this->title = $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Группа '.$this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="training-group-view">
    <div class="substrate">
        <div class="flexx">
            <h1>
                <?= Html::encode($this->title) ?>
            </h1>
            <h3>
                <?= $model->getRawArchive(); ?>
            </h3>
        </div>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
                <div style="margin: 1.1em 1em">
                    <?= HtmlBuilder::createDualityButton(
                        ['Создать журнал', 'Открыть журнал'],
                        [Url::to(['generate-journal', 'id' => $model->id]), Url::to(['educational/journal/view', 'id' => $model->id])],
                        [['btn', 'btn-success'], ['btn', 'btn-primary']],
                        $journalState == JournalService::JOURNAL_EMPTY
                    ); ?>
                </div>
                <div style="margin: 1.1em 1em 1.1em 0">
                    <?= HtmlBuilder::createDualityButton(
                        ['Архивировать', 'Разархивировать'],
                        [Url::to(['archive-group', 'id' => $model->id]), Url::to(['unarchive-group', 'id' => $model->id])],
                        [['btn', 'btn-success'], ['btn', 'btn-primary']],
                        !$model->trainingGroup->isArchive()
                    ); ?>
                </div>
                <?php if (
                        Yii::$app->rubac->checkPermission(Yii::$app->rubac->authId(), 'edit_branch_groups') ||
                        Yii::$app->rubac->checkPermission(Yii::$app->rubac->authId(), 'edit_all_groups')
                ): ?>
                    <div style="margin: 1.1em 1em 1.1em 0">
                        <?= HtmlBuilder::createDualityButton(
                            ['Допустить к защите', 'Не допускать к защите'],
                            [Url::to(['pitch-confirm-group', 'id' => $model->id]), Url::to(['pitch-decline-group', 'id' => $model->id])],
                            [['btn', 'btn-success'], ['btn', 'btn-primary']],
                            !$model->trainingGroup->isPitchConfirm()
                        ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="substrate">
        <div class="flexx">

        </div>
        <div class="flexx">
            <?php
            if ($journalState == JournalService::JOURNAL_EXIST) {
                $links = array_merge(
                    ButtonsFormatter::anyOneLink(
                        'Скачать КУГ',
                        Yii::$app->frontUrls::TRAINING_GROUP_KUG,
                        ButtonsFormatter::BTN_SUCCESS,
                        '',
                        ButtonsFormatter::createParameterLink($model->id)),
                    ButtonsFormatter::anyOneLink(
                        'Сформировать протокол',
                        Yii::$app->frontUrls::TRAINING_GROUP_PROTOCOL,
                        ButtonsFormatter::BTN_SUCCESS,
                        '',
                        ButtonsFormatter::createParameterLink($model->id)),
                    ButtonsFormatter::anyOneLink(
                        'Скачать журнал',
                        Yii::$app->frontUrls::TRAINING_GROUP_DOWNLOAD_JOURNAL,
                        ButtonsFormatter::BTN_SUCCESS,
                        '',
                        ButtonsFormatter::createParameterLink($model->id)),
                );
                echo HtmlBuilder::createGroupButton($links);
            }
            ?>
        </div>
        <div class="flexx">
            <?php
            $links = array_merge(
                ButtonsFormatter::anyOneLink(
                    'Простить ошибки',
                    Yii::$app->frontUrls::TRAINING_GROUP_AMNESTY,
                    ButtonsFormatter::BTN_WARNING,
                    '',
                    ButtonsFormatter::createParameterLink($model->id)),
                ButtonsFormatter::anyOneLink(
                    'Сгенерировать сертификаты',
                    Yii::$app->frontUrls::CERTIFICATE_CREATE,
                    ButtonsFormatter::BTN_PRIMARY,
                    '',
                    ButtonsFormatter::createParameterLink($model->id)),
                ButtonsFormatter::anyOneLink(
                    'Отправить сертификаты',
                    Yii::$app->frontUrls::CERTIFICATE_SEND_ALL,
                    ButtonsFormatter::BTN_SUCCESS,
                    '',
                    ButtonsFormatter::createParameterLink($model->id, 'groupId')),
            );
            echo HtmlBuilder::createGroupButton($links);
            ?>
        </div>
    </div>

    <?= HtmlBuilder::createErrorsBlock(TrainingGroupWork::tableName(), $model->id) ?>
    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Отдел
                    </div>
                    <div class="field-date">
                        <?= $model->getBranch(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Педагоги
                    </div>
                    <div class="field-date">
                        <?= $model->getTeachersRaw(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Период обучения
                    </div>
                    <div class="field-date">
                        <?= $model->getTrainingPeriod(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Программа и форма обучения</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Образ. программа
                    </div>
                    <div class="field-date">
                        <?= $model->getTrainingProgramRaw(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Форма обучения
                    </div>
                    <div class="field-date">
                        <?= $model->getFormStudy(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Приказы</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Загрузка приказов
                    </div>
                    <div class="field-date">
                        <?= $model->getConsentOrders(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Документы
                    </div>
                    <div class="field-date">
                        <?= $model->getOrdersRaw(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Дополнительная информация</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Выработка чел/ч
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyManHoursPercent(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во детей
                    </div>
                    <div class="field-date">
                        <?= $model->getCountParticipants(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во занятий
                    </div>
                    <div class="field-date">
                        <?= $model->getCountLessons(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Допущена к итоговому контролю
                    </div>
                    <div class="field-date">
                        <?= $model->getProtectionConfirm(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Учебный график и состав</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Расписание
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyLessons() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Состав группы
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyParticipants() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Итоговый контроль
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyFinalControl(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?= $model->getFullPhotoFiles(); ?><div>Фотоматериалы</div></div>
                    <div class="file-block-center"><?= $model->getFullPresentationFiles(); ?><div>Презентационные материалы</div></div>
                    <div class="file-block-center"><?= $model->getFullWorkFiles(); ?><div>Рабочие материалы</div></div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Свойства</div>
                <div class="flexx">
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            Создатель карточки
                        </div>
                        <div class="field-date">
                            <?= $model->getCreatorName(); ?>
                        </div>
                    </div>
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            Последний редактор
                        </div>
                        <div class="field-date">
                            <?= $model->getLastEditorName(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
