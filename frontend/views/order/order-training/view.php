<?php

use common\helpers\html\HtmlBuilder;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderTrainingWork;
use common\helpers\DateFormatter;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model OrderTrainingWork */
/* @var $modelResponsiblePeople */
/* @var $groups */
/* @var $participants */
/* @var $error */
/* @var $buttonsAct */

$this->title = $model->order_name;
$this->params['breadcrumbs'][] = ['label' => 'Приказы об образовательной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-training-view">
    <?php
    switch ($error) {
        case DocumentOrderWork::ERROR_DATE_PARTICIPANT:
            echo HtmlBuilder::createMessage(HtmlBuilder::TYPE_WARNING,'Невозможно применить действие к ученикам.', 'Ошибка выбора даты приказа');
            break;
        case DocumentOrderWork::ERROR_RELATION:
            echo HtmlBuilder::createMessage(HtmlBuilder::TYPE_WARNING,'Невозможно применить действие к ученикам.', 'Выбранные обучающиеся задействованы в других приказах');
            break;
    }
    ?>

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct ?>
            </div>
        </div>
    </div>
    <?= HtmlBuilder::createErrorsBlock(DocumentOrderWork::tableName(), $model->id) ?>
    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Рег. номер
                    </div>
                    <div class="field-date">
                        <?= $model->getNumberPostfix() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Наименование приказа
                    </div>
                    <div class="field-date">
                        <?= $model->order_name ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата приказа
                    </div>
                    <div class="field-date">
                        <?= DateFormatter::format($model->order_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot) ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Образовательное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Группы
                    </div>
                    <div class="field-date">
                        <?= HtmlBuilder::createAccordion($groups) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Обучающиеся
                    </div>
                    <div class="field-date">
                        <?= HtmlBuilder::createAccordion($participants) ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">
                    Ключевые слова
                </div>
                <div class="card-field">
                    <div class="field-date">
                        <?= $model->getKeyWords() ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Сотрудники</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Проект вносит
                    </div>
                    <div class="field-date">
                        <?= $model->bringWork ? $model->bringWork->peopleWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) : '---' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Исполнитель
                    </div>
                    <div class="field-date">
                        <?= $model->executorWork ? $model->executorWork->peopleWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) : '---' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Ответственные
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyResponsibles() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?= $model->getFullScan(); ?><div>Сканы</div></div>
                    <div class="file-block-center"><?= $model->getFullDoc(); ?><div>Редактируемые</div></div>
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
                            <?= $model->creatorWork ? $model->creatorWork->getFullName() : '---'?>
                        </div>
                    </div>
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            Последний редактор
                        </div>
                        <div class="field-date">
                            <?= $model->lastUpdateWork ? $model->lastUpdateWork->getFullName() : '---' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
