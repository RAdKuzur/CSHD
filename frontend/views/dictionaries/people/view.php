<?php

use common\helpers\DateFormatter;
use common\helpers\html\HtmlBuilder;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\general\PeopleWork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model PeopleWork */
/* @var $groupsList string */
/* @var $studentAchievements string */
/* @var $responsibilities string */
/* @var $positions string */
/* @var $buttonsAct */

$this->title = $model->getFIO(PersonInterface::FIO_FULL);
$this->params['breadcrumbs'][] = ['label' => 'Люди', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="people-view">

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
                        ФИО
                    </div>
                    <div class="field-date">
                        <?= $model->getFIO(PersonInterface::FIO_FULL) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата рождения
                    </div>
                    <div class="field-date">
                        <?= DateFormatter::format($model->birthdate, DateFormatter::Ymd_dash, DateFormatter::dmY_dot) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Пол
                    </div>
                    <div class="field-date">
                        <?= $model->getSexString() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Рабочая информация</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Организации и должности
                    </div>
                    <div class="field-date">
                        <?= $positions ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Отдел по ТД
                    </div>
                    <div class="field-date">
                        <?= Yii::$app->branches->get($model->branch) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Уникальный ID
                    </div>
                    <div class="field-date">
                        <?= $model->short ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Ответственности
                    </div>
                    <div class="field-date">
                        <?= HtmlBuilder::createAccordion($responsibilities) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2" style="flex-basis: 60%">
            <div class="card-set">
                <div class="card-head">Образовательная деятельность</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Группы
                    </div>
                    <div class="field-date">
                        <?= HtmlBuilder::createAccordion($groupsList) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Достижения учеников
                    </div>
                    <div class="field-date">
                        <?= HtmlBuilder::createAccordion($studentAchievements) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
