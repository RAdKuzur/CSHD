<?php

use common\helpers\html\HtmlBuilder;
use frontend\models\work\event\EventWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model EventWork */
/* @var $buttonsAct */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Мероприятия', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-view">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
            </div>
        </div>
    </div>
    <?= HtmlBuilder::createErrorsBlock(EventWork::tableName(), $model->id) ?>
    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Период проведения
                    </div>
                    <div class="field-date">
                        <?= $model->getDatePeriod(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Адрес
                    </div>
                    <div class="field-date">
                        <?= $model->getAddress(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Тип
                    </div>
                    <div class="field-date">
                        <?= $model->getEventType(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Форма
                    </div>
                    <div class="field-date">
                        <?= $model->getEventForm(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Уровень
                    </div>
                    <div class="field-date">
                        <?= $model->getEventLevel(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Формат проведения
                    </div>
                    <div class="field-date">
                        <?= $model->getEventWay(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Информация об участниках</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Общее кол-во детей:
                    </div>
                    <div class="field-date">
                        <?= $model->getChildParticipantsCount(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во детей от РШТ:
                    </div>
                    <div class="field-date">
                        <?= $model->getChildRSTParticipantsCount(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Возрастные ограничения:
                    </div>
                    <div class="field-date">
                        <?= $model->getAgeRestrictions(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во преподавателей:
                    </div>
                    <div class="field-date">
                        <?= $model->getTeacherParticipantsCount(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во других участников:
                    </div>
                    <div class="field-date">
                        <?= $model->getOtherParticipantsCount(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Дополнительная информация</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Сферы участия
                    </div>
                    <div class="field-date">
                        <?= $model->getScopesString(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Мероприятие проводит
                    </div>
                    <div class="field-date">
                        <?= $model->getEventBranches(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Ответственные работники
                    </div>
                    <div class="field-date">
                        <?= $model->getResponsibles(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Содержит образ. программы
                    </div>
                    <div class="field-date">
                        <?= $model->getContainsEducation(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Примечание
                    </div>
                    <div class="field-date">
                        <?= $model->getComment(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Связанные документы</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Приказ
                    </div>
                    <div class="field-date">
                        <?= $model->getOrderNameRaw(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Положение
                    </div>
                    <div class="field-date">
                        <?= $model->getRegulationRaw(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Связанные группы</div>
                <div class="card-field flexx">
                    <div class="field-date">
                        <?= $model->getEventGroupRaw(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Ключевые слова</div>
                <div class="card-field flexx">
                    <div class="field-date">
                        <?= $model->getKeyWord(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?= $model->getFullProtocol(); ?><div>Протокол мероприятия</div></div>
                    <div class="file-block-center"><?= $model->getFullPhoto(); ?><div>Фотоматериалы</div></div>
                    <div class="file-block-center"><?= $model->getFullReporting(); ?><div>Явочные документы</div></div>
                    <div class="file-block-center"><?= $model->getFullOther(); ?><div>Другие файлы</div></div>
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
