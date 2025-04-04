<?php

use common\helpers\files\FilesHelper;
use frontend\models\work\dictionaries\AuditoriumWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model AuditoriumWork */
/* @var $buttonsAct */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Помещения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auditorium-view">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Глобальный номер
                    </div>
                    <div class="field-date">
                        <?= $model->name ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Название
                    </div>
                    <div class="field-date">
                        <?= $model->text ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Отдел
                    </div>
                    <div class="field-date">
                        <?= Yii::$app->branches->get($model->branch) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Площадь (кв.м.)
                    </div>
                    <div class="field-date">
                        <?= $model->square ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Дополнительно</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Площадь учитывается при подсчете
                    </div>
                    <div class="field-date">
                        <?= $model->getIncludeSquarePretty() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Предназначен для обр. деят.
                    </div>
                    <div class="field-date">
                        <?= $model->getEducationPretty()  ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во окон
                    </div>
                    <div class="field-date">
                        <?= $model->window_count ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?= $model->getFullOther(); ?><div>Доп. материалы</div></div>
                </div>
            </div>
        </div>
    </div>

</div>
