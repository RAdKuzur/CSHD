<?php

use frontend\models\work\dictionaries\CompanyWork;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model CompanyWork */
/* @var $buttonsAct */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="company-view">

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
                        Тип
                    </div>
                    <div class="field-date">
                        <?= Yii::$app->companyType->get($model->company_type) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Название
                    </div>
                    <div class="field-date">
                        <?= $model->name ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Краткое название
                    </div>
                    <div class="field-date">
                        <?= $model->short_name ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Данные контрагента</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        ИНН
                    </div>
                    <div class="field-date">
                        <?= $model->inn ? : '---' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        ОКВЭД
                    </div>
                    <div class="field-date">
                        <?= $model->okved ? : '---' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Категория СМСП
                    </div>
                    <div class="field-date">
                        <?= $model->category_smsp ? Yii::$app->categorySmsp->get($model->category_smsp) : '---' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Форма собственности
                    </div>
                    <div class="field-date">
                        <?= $model->ownership_type ? Yii::$app->ownershipType->get($model->ownership_type) : '---' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        E-mail
                    </div>
                    <div class="field-date">
                        <?= $model->email ? : '---' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        ФИО руководителя
                    </div>
                    <div class="field-date">
                        <?= $model->head_fio ? : '---' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Номер телефона
                    </div>
                    <div class="field-date">
                        <?= $model->phone_number ? : '---' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Сайт
                    </div>
                    <div class="field-date">
                        <?= $model->site ? : '---' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Комментарий
                    </div>
                    <div class="field-date">
                        <?= $model->comment ? : '---' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
