<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrainingProgramWork */
/* @var $thematicPlan array */
/* @var $buttonsAct */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="training-program-view">
    <div class="substrate">
        <div class="flexx">
            <h1>
                <?= Html::encode($this->title) ?>
            </h1>
            <h3>
                <?= $model->getRawActual(); ?>
            </h3>
        </div>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
            </div>
        </div>
    </div>
    <?= HtmlBuilder::createErrorsBlock(TrainingProgramWork::tableName(), $model->id) ?>
    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Актуальность программы
                    </div>
                    <div class="field-date">
                        <?= $model->getActual(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Уровень сложности
                    </div>
                    <div class="field-date">
                        <?= $model->getLevelNumber(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Возрастные ограничения
                    </div>
                    <div class="field-date">
                        <?= $model->getAgePeriod(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Объем программы
                    </div>
                    <div class="field-date">
                        <?= $model->getCapacityAndHour(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Направленность и направление</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Направленность
                    </div>
                    <div class="field-date">
                        <?= $model->getFocusString(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Тематическое направление
                    </div>
                    <div class="field-date">
                        <?= $model->getfullDirectionName() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Форма и место реализации</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Форма реализации
                    </div>
                    <div class="field-date">
                        <?= $model->getAllowRemote() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Сетевая форма обучения
                    </div>
                    <div class="field-date">
                        <?= $model->getIsNetwork(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Место реализации
                    </div>
                    <div class="field-date">
                        <?= $model->getBranchString(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Итоговая форма контроля
                    </div>
                    <div class="field-date">
                        <?= $model->getCertificateType(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Дополнительная информация</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Учебно-тематический план
                    </div>
                    <div class="field-date">
                        <?= $model->getThematicPlaneRaw(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Описание
                    </div>
                    <div class="field-date">
                        <?= $model->getDescription(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Педагогический совет</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата пед. совета
                    </div>
                    <div class="field-date">
                        <?= $model->getPedCouncilDate(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Номер протокола
                    </div>
                    <div class="field-date">
                        <?= $model->getPedCouncilNumber(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Составители
                    </div>
                    <div class="field-date">
                        <?= $model->getAuthorString(StringFormatter::FORMAT_LINK); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Учебные группы</div>
                <div class="card-field flexx">
                    <div class="field-date">
                        <?= $model->getTrainingProgramRaw(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Ключевые слова</div>
                <div class="card-field flexx">
                    <div class="field-date">
                        <?= $model->getKeyWords(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?= $model->getFullMainFiles(); ?><div>Документ программы</div></div>
                    <div class="file-block-center"><?= $model->getFullDoc(); ?><div>Редактируемый документ</div></div>
                    <div class="file-block-center"><?= $model->getFullContract(); ?><div>Договор о сетевой форме</div></div>
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