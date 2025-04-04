<?php

use common\helpers\DateFormatter;
use common\helpers\html\HtmlBuilder;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonInterface;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ForeignEventParticipantsWork */
/* @var $buttonsAct */

$this->title = $model->getFIO(PersonInterface::FIO_FULL);
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>

    .badge {
        padding: 3px 9px 4px;
        font-size: 13px;
        font-weight: bold;
        white-space: nowrap;
        color: #ffffff;
        background-color: #999999;
        -webkit-border-radius: 9px;
        -moz-border-radius: 9px;
        border-radius: 9px;
    }
    .badge:hover {
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }
    .badge-error {
        background-color: #b94a48;
    }
    .badge-error:hover {
        background-color: #953b39;
    }
    .badge-success {
        background-color: #468847;
    }
    .badge-success:hover {
        background-color: #356635;
    }

</style>

<div class="foreign-event-participants-view">

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
                <div class="card-field flexx">
                    <div class="field-title">
                        E-mail
                    </div>
                    <div class="field-date">
                        <?= $model->email ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Участие в образовательных программах</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Группы
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyGroups() ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2" style="flex-basis: 60%;">
            <div class="card-set">
                <div class="card-head">Участие в мероприятиях</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Достижения
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyAchieves() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Мероприятия
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyEvents() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Персональные данные</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Разглашение
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyPersonals() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
