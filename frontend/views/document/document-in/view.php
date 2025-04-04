<?php

use common\helpers\html\HtmlBuilder;
use frontend\models\work\document_in_out\DocumentInWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\document_in_out\DocumentInWork */
/* @var $buttonsAct */

$this->title = $model->document_theme;
$this->params['breadcrumbs'][] = ['label' => 'Входящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>


<div class="document-in-view">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct ?>
            </div>
        </div>
    </div>
    <?= HtmlBuilder::createErrorsBlock(DocumentInWork::tableName(), $model->id) ?>
    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Имя
                    </div>
                    <div class="field-date">
                        <?= $model->getFullName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Тип
                    </div>
                    <div class="field-date">
                        Входящая документация
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Тема
                    </div>
                    <div class="field-date">
                        <?= $model->getDocumentTheme() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Способ получения
                    </div>
                    <div class="field-date">
                        <?= $model->getSendMethodName() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">От кого</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Корреспондент
                    </div>
                    <div class="field-date">
                        <?= $model->getCompanyName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Должность и ФИО
                    </div>
                    <div class="field-date">
                        <?= $model->getCorrespondentName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата и номер
                    </div>
                    <div class="field-date">
                        <?= $model->getRealDate() . ' № ' . $model->getRealNumber() ?>
                    </div>
                </div>
            </div>
            <?php if ($model->getNeedAnswer()) : ?>
            <div class="card-set">
                <div class="card-head">Ответ</div>
                <?php if ($model->getAnswerNotEmpty()) : ?>
                <div class="card-field flexx">
                    <div class="field-title">
                        Документ
                    </div>
                    <div class="field-date">
                        <?= $model->getAnswer() ?>
                    </div>
                </div>
                <?php else : ?>
                <div class="card-field flexx">
                    <div class="field-title">
                        Ответственный
                    </div>
                    <div class="field-date">
                        <?= $model->getResponsibleName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Cрок ответа
                    </div>
                    <div class="field-date">
                        <?= $model->getResponsibleDate() ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Дата и номер</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        № п/п
                    </div>
                    <div class="field-date">
                        <?= $model->getFullNumber() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата
                    </div>
                    <div class="field-date">
                        <?= $model->getLocalDate() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Ключевые слова</div>
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
                    <div class="file-block-center"><?= $model->getFullApp(); ?><div>Приложения</div></div>
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
                        <?= $model->getCreatorName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title field-option">
                        Последний редактор
                    </div>
                    <div class="field-date">
                        <?= $model->getLastEditorName() ?>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
