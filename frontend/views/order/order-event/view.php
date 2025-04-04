<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderEventWork;
use common\helpers\DateFormatter;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model OrderEventWork */
/* @var $modelResponsiblePeople */
/* @var ForeignEventWork $foreignEvent */
/* @var $actTable */
/* @var $buttonsAct */

$this->title = $model->order_name;
$this->params['breadcrumbs'][] = ['label' => 'Приказы о мероприятиях'];
\yii\web\YiiAsset::register($this);
?>
<div class="order-main-view">
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
                <div class="card-head">Мероприятиевое</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Мероприятие
                    </div>
                    <div class="field-date">
                        <?= StringFormatter::stringAsLink($foreignEvent->name, Url::to([Yii::$app->frontUrls::FOREIGN_EVENT_VIEW, 'id' => $foreignEvent->id])) ?>
                    </div>
                </div>
            </div>
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
        </div>
        <div class="card-block-2">
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
                            <?= $model->creatorWork ? $model->creatorWork->getFullName() : '---' ?>
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
