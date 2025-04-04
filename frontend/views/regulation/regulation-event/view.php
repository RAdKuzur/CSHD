<?php

use common\components\dictionaries\base\RegulationTypeDictionary;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model RegulationWork */
/* @var $buttonsAct */

$this->title = $model->getShortName();

$this->params['breadcrumbs'][] = ['label' => Yii::$app->regulationType->get(RegulationTypeDictionary::TYPE_EVENT), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="regulation-view">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата
                    </div>
                    <div class="field-date">
                        <?= $model->getDate(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Состояние
                    </div>
                    <div class="field-date">
                        <?= $model->getStates(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Краткое наименование
                    </div>
                    <div class="field-date">
                        <?= $model->getShortName(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Полное наименование
                    </div>
                    <div class="field-date">
                        <?= $model->getName(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Документ</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Приказ
                    </div>
                    <div class="field-date">
                        <?= $model->getOrderName(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Файл</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?= $model->getFullScan(); ?><div>Скан</div></div>
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
