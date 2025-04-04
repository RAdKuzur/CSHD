<?php

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use frontend\forms\event\ForeignEventForm;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\event\ForeignEventWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ForeignEventForm */
/* @var $buttonsAct */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Учет достижений в мероприятиях', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="foreign-event-view">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
            </div>
        </div>
    </div>
    <?= HtmlBuilder::createErrorsBlock(ForeignEventWork::tableName(), $model->id) ?>
    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Название
                    </div>
                    <div class="field-date">
                        <?= $model->name; ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Даты проведения
                    </div>
                    <div class="field-date">
                        <?= $model->getEventPeriod() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Город
                    </div>
                    <div class="field-date">
                        <?= $model->city; ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">О мероприятии</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Формат проведения
                    </div>
                    <div class="field-date">
                        <?= Yii::$app->eventWay->get($model->format) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Уровень
                    </div>
                    <div class="field-date">
                        <?= Yii::$app->eventLevel->get($model->level) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Возраст участников
                    </div>
                    <div class="field-date">
                        <?= $model->getAgeRange() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Учет достижений</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Участники
                    </div>
                    <div class="field-date">
                        <?= HtmlBuilder::createAccordion($model->getParticipantsLink()) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Достижения
                    </div>
                    <div class="field-date">
                        <?= HtmlBuilder::createAccordion($model->getAchievementsLink()) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <?php if ($model->isBusinessTrip()): ?>
            <div class="card-set">
                <div class="card-head">Командировка</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Сопровождающий
                    </div>
                    <div class="field-date">
                        <?= $model->event->escortWork ? $model->event->escortWork->peopleWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) : '---'; ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Приказ о направлении
                    </div>
                    <div class="field-date">
                        <?=
                            $model->event->orderBusinessTripWork ?
                            StringFormatter::stringAsLink(
                                    $model->event->orderBusinessTripWork->getFullName(),
                                    Url::to([Yii::$app->frontUrls::ORDER_MAIN_VIEW, 'id' => $model->id])) :
                            '---';
                        ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="card-set">
                <div class="card-head">Приказы</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Приказ об участии
                    </div>
                    <div class="field-date">
                        <?= $model->event->orderParticipantWork ?
                            StringFormatter::stringAsLink(
                                $model->event->orderParticipantWork->getFullName(),
                                Url::to([Yii::$app->frontUrls::ORDER_MAIN_VIEW, 'id' => $model->id])) :
                            '---'; ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Доп. приказ
                    </div>
                    <div class="field-date">
                        <?=
                        $model->event->addOrderParticipantWork ?
                            StringFormatter::stringAsLink(
                                $model->event->addOrderParticipantWork->getFullName(),
                                Url::to([Yii::$app->frontUrls::ORDER_MAIN_VIEW, 'id' => $model->id])) :
                            '---';
                        ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Ключевые слова</div>
                <div class="card-field flexx">
                    <div class="field-date">
                        <?= $model->keyWords ?>
                    </div>
                </div>
            </div>

            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?= $model->event->getFullDoc(); ?><div>Документ о достижениях</div></div>
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
                            <?= $model->event->creatorWork ? $model->event->creatorWork->getFullName() : '---'; ?>
                        </div>
                    </div>
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            Последний редактор
                        </div>
                        <div class="field-date">
                            <?= $model->event->lastEditWork ? $model->event->lastEditWork->getFullName() : '---'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
